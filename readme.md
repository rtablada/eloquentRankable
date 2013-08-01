Eloquent Rankable
==================

This package makes rankable models for sorting quick and easy.

Setting up a Rankable Model
---------------------------

Making Rankable Models is just as easy as creating regular Eloquent Models with just one more property `protected $metricsWeight`!

An example model would look something like this:

```php
use Rtablada\EloquentRankable\RankableModel;

class Friend extends RankableModel
{
	protected $metricWeights = array(
		'search' => 0.8,
		'name' => 0.2
	);

	protected $fillable = array('name', 'rank');
}
```

In your Schema remember to include a `rank` column (I suggest using a Decimal fieldtype with a 10 digits and 4 decimals).

The `$metricWeight` Property
---------------------------

The `$metricWeight` property is an easy way to modify the ranking property of your models.
You can set weights for whenever you use an `updateMetric*` function.

So if you want to update a model's rank when you get a result in a search you could run `$model->updateMetricSearch()` which will raise the ranking by 0.8 points.

These `updateMetric` functions can also be used in mutators or accessors.

```php
public function setNameAttribute($value)
{
	$this->attributes['name'] = $value;
	$this->updateMetricName();
}
```

Rank Queries
---------------------------

Any time you want to get results already sorted descending by `rank` you can just prepend your wanted query builder function with `rank`.
For example:

```php
$friends = Friend::rankAll();
$friendsPaginated = Friend::rankPaginate();
```


Updating Rank In Comparison To Other Entries
---------------------------

The model also gives you the ability to `rankBefore`, `rankBetween`, or `rankAfter` another model instance.

```php
$friendLow = Friend::find(1);
$friendHigh = Friend::find(1);

$friendLow->rankBefore($friendHigh);
```

Updating All Entries With Sorted `ids`
---------------------------

For uses such as Javascript Web Apps, Rankable gives you a quick and easy way to update the rankings between entries.

```php
$desiredIds = array(1,2,3);
$friends = Friend::rankOrderSet($desiredIds);
```
