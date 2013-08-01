<?php namespace Rtablada\EloquentRankable;

use Illuminate\Database\Eloquent\Collection;

class RankableCollection extends Collection
{
	/**
	 * Checks order of collection and updates rank to new order
	 *
	 * @param  array  $idResults [description]
	 * @return [type]            [description]
	 */
	public function updateRanksByIds(array $idResults)
	{
		$this->sortByRank();

		$i = 0;
		$length = count($this->items) - 1;
		$last = null;

		foreach ($this->items as $item) {
			if ($item->id != $idResults[$i]) {
				$result = $this->find($idResults[$i]);
				if ($i === 0) {
					$result->rankBefore($item);
				} else {
					$result->rankBetween($item, $last);
				}
				return $this->updateRanksByIds($idResults);
			}
			$i ++;
			$last = $item;
		}

		return $this->sortByRank();
	}

	public function sortByRank()
	{
		$collection = $this->sortBy(function($model)
		{
			return - $model->rank;
		});

		return $collection;
	}
}
