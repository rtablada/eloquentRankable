<?php namespace Rtablada\EloquentRankable;

use Illuminate\Database\Eloquent\Collection;

abstract class RankableCollection extends Collection
{
	/**
	 * Checks order of collection and updates rank to new order
	 *
	 * @param  array  $idResults [description]
	 * @return [type]            [description]
	 */
	public function updateRanksByIds(array $idResults)
	{
		foreach ($this->items as $key => $item) {
			if ($item->id !== $idResults[$key]) {
				if ($key === 0) {
					$resultItem = $items->find($idResults[$key]);
					$resultItem->rankBefore($item);
				} else {
					$resultItem->rankBetween($items[$key - 1], $item);
				}

				if (! $item->globalRank) {
					return $this->sortByRank();
				} else {
					return $this->updateRanksByIds($idResults);
				}
			}
		}
	}

	public function sortByRank()
	{
		return $this->sortBy(function($model)
		{
			return $model->rank;
		});
	}
}
