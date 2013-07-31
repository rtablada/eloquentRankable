<?php namespace Rtablada\EloquentRankable;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class RankableModel extends Eloquent
{
	/**
	 * Metrics array used to store values to be modified
	 * using the touch magic methods
	 *
	 * @var array
	 */
	protected $metricWeights = array();

	/**
	 * Determines if a ranking sort will end after one check of
	 * rank order.
	 *
	 * @var boolean
	 */
	public $globalRank = false;

	/**
	 * Create a new Rankable Eloquent Collection instance.
	 *
	 * @param  array  $models
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new RankableCollection($models);
	}

	/**
	 * Handle dynamic method calls into the method.
	 * Checks for Metric Touch Availability
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if ($metric = $this->findMetric($method)) {
			$parameters[0] = empty($parameters) ? 1 : $parameters[0];
			return $this->updateMetric($metric, $parameters[0]);
		} else {
			parent::__call($method, $parameters);
		}
	}

	public function rankBefore(RankableModel $model)
	{
		$lowerRank = $model->rank;
		$this->attributes['rank'] = $lowerRank + 1;
		$this->save();
	}

	public function rankBetween(RankableModel $high, RankableModel $low)
	{
		$lowerRank = $low->rank;
		$highererRank = $higher->rank;
		$this->attributes['rank'] = ($highererRank + $lowerRank) / 2;
		$this->save();
	}

	/**
	 * Checks to see if method call began with updateMetric
	 * Checks to see if metric key exists in metrics property
	 *
	 * @param  string $method
	 * @param  array $parameters
	 * @return string
	 */
	protected function findMetric($method)
	{
		$matches = array();
		if (preg_match('/updateMetric(.*)/', $method, $matches)) {
			$metric = $matches[1];
			if (array_key_exists($metric, $this->metricWeights)) {
				return $metric;
			}
		}

		return false;
	}

	/**
	 * Updates the rank attribute against the metric weight.
	 *
	 * @param  string  $metric
	 * @param  integer $value
	 * @return null
	 */
	protected function updateMetric($metric, $value = 1)
	{
		$this->attributes['rank'] += $value * $this->metricWeights[$metric];
	}
}
