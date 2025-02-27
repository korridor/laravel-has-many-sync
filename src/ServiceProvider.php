<?php

declare(strict_types=1);

namespace Korridor\LaravelHasManySync;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

        HasMany::macro('sync', function (array $data, bool $deleting = true, bool $throwOnIdNotInScope = true): array {
            $changes = [
                'created' => [], 'deleted' => [], 'updated' => [],
            ];

            /** @var HasMany<Model, Model> $this */

            // Get the primary key.
            $relatedKeyName = $this->getRelated()->getKeyName();

            // Get the current key values.
            $currentIds = $this->newQuery()->pluck($relatedKeyName)->all();

            // Cast the given key to an integer if it is numeric.
            $castKey = function ($value) {
                if (is_null($value)) {
                    return null;
                }

                return is_numeric($value) ? (int) $value : (string) $value;
            };

            // Cast the given keys to integers if they are numeric and string otherwise.
            $castKeys = function ($keys) use ($castKey): array {
                return (array) array_map(function ($key) use ($castKey) {
                    return $castKey($key);
                }, $keys);
            };

            // The new ids, without null values
            $dataIds = Arr::where($castKeys(Arr::pluck($data, $relatedKeyName)), function ($value) {
                return !is_null($value);
            });

            $problemKeys = array_diff($dataIds, $currentIds);
            if ($throwOnIdNotInScope && count($problemKeys) > 0) {
                throw (new ModelNotFoundException())->setModel(
                    get_class($this->getRelated()),
                    $problemKeys
                );
            }

            // Get any non-matching rows.
            $deletedKeys = array_diff($currentIds, $dataIds);

            if ($deleting && count($deletedKeys) > 0) {
                $this->getRelated()->destroy($deletedKeys);
                $changes['deleted'] = $deletedKeys;
            }

            // Separate the submitted data into "update" and "new"
            // We determine "newRows" as those whose $relatedKeyName (usually 'id') is null.
            $newRows = Arr::where($data, function (array $row) use ($relatedKeyName) {
                $id = Arr::get($row, $relatedKeyName);
                return $id === null;
            });

            // We determine "updateRows" as those whose $relatedKeyName (usually 'id') is set, not null.
            $updateRows = Arr::where($data, function (array $row) use ($relatedKeyName, $problemKeys) {
                $id = Arr::get($row, $relatedKeyName);
                return $id !== null && !in_array($id, $problemKeys, true);
            });

            if (count($newRows) > 0) {
                $newRecords = $this->createMany($newRows);
                $changes['created'] = $castKeys(
                    $newRecords->pluck($relatedKeyName)->toArray()
                );
            }

            foreach ($updateRows as $row) {
                $updateModel = $this->getRelated()
                    ->newQuery()
                    ->where($relatedKeyName, $castKey(Arr::get($row, $relatedKeyName)))
                    ->firstOrFail();

                $updateModel->update($row);
            }

            $changes['updated'] = $castKeys(Arr::pluck($updateRows, $relatedKeyName));

            return $changes;
        });
    }
}
