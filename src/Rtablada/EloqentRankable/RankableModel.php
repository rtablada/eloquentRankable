<?php namespace Rtablada\EloquentRankable;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class RankableModel extends Eloquent
{
	/**
	 * Metrics array used to store values to be modified
	 * using the touch magic methods
	 * @var array
	 */
	protected $metricWeights = array();

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
	 * Updates the rank attribute against the metric weight
	 * @param  string  $metric
	 * @param  integer $value
	 * @return null
	 */
	protected function updateMetric($metric, $value = 1)
	{
		$this->attributes['rank'] += $value * $this->metricWeights[$metric];
	}
}
