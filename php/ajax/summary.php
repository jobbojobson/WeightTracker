<?php
header("Content-Type: text/html");
require(__DIR__.'/../include/db/WeightDatabase.php');

$dbo = new WeightDatabase();

$user = $dbo->getUser();

$currentWeight = $dbo->getCurrentWeight();

$maxWeight = $dbo->getMaxWeight();

function getPounds( $kg ){
	return $kg * 2.2046;
}

function getStones( $lbs ){
	return strval(intval($lbs / 14)).' '.strval(intval($lbs % 14));
}

function getBMI($user, $currentWeight){
	
	#return $currentWeight.' : '.$user['height'].' : '.($currentWeight / (($user['height'] / 100)^2));
	
	return floor($currentWeight / pow(($user['height'] / 100), 2));
}

function getBMR($user, $currentWeight){
	#=IF(B1 = "Male", (88.362 + (13.397 * B6) + (4.799 * B3) - (5.677 * B4)), (447.593 + (9.247 * B6) + (3.098 * B3) + (4.33 * B4))) * VLOOKUP(B5, J4:K8, 2, FALSE)

	$userAge = date_diff(date_create($user['dob']), date_create(date("Y-m-d")))->format('%y');
	
	if($user['gender'] == 'M'){		
		$output = (88.362 + (13.397 * $currentWeight) + (4.799 * $user['height']) - (5.677 * $userAge));
	} else {
		$output = (447.593 + (9.247 * $currentWeight) + (3.098 * $user['height']) - (5.677 * $userAge));
	}
	
	return ($output * $user['activity_multiplier']);
	
}


?>

<div class="table-responsive mb-2">
	<table class="table table-sm align-middle table-bordered table-summary">
		<thead class="table-dark">
			<tr>
				<th></th>
				<th class="num-col-small">KG</th>
				<th class="num-col-small">Pounds</th>
				<th class="num-col-small">Stone</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Target Weight</td>
				<td><?php echo number_format(round($user['goal_kg'], 1), 1) ?></td>
				<td><?php echo number_format(round(getPounds($user['goal_kg']), 2), 2) ?></td>
				<td><?php echo getStones(getPounds($user['goal_kg'])) ?></td>
			</tr>
			<tr>
				<td><i id="trend_flag" class="bi"></i> Current Weight</td>
				<td><?php echo number_format(round($currentWeight, 1), 1) ?></td>
				<td><?php echo number_format(round(getPounds($currentWeight), 2), 2) ?></td>
				<td><?php echo getStones(getPounds($currentWeight)) ?></td>
			</tr>
			<tr>
				<td>Lost</td>
				<td><?php echo number_format(round($maxWeight - $currentWeight, 1), 1) ?></td>
				<td><?php echo number_format(round(getPounds($maxWeight) - getPounds($currentWeight), 2), 2) ?></td>
				<td><?php echo getStones(getPounds($maxWeight) - getPounds($currentWeight)) ?></td>
			</tr>
			<tr>
				<td>Remaining</td>
				<td><?php echo number_format(round($currentWeight - $user['goal_kg'], 1), 1) ?></td>
				<td><?php echo number_format(round(getPounds($currentWeight) - getPounds($user['goal_kg']), 2), 2) ?></td>
				<td><?php echo getStones(getPounds($currentWeight) - getPounds($user['goal_kg'])) ?></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="table-responsive">
	<table class="table table-sm align-middle table-bordered">
		<thead class="table-dark">
			<tr>
				<th>Current BMI</th>
				<th>Current BMR</th>
				<th>BMR Minus Deficit</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo getBMI($user, $currentWeight) ?></td>
				<td><?php $bmr = getBMR($user, $currentWeight); echo number_format(round($bmr, 2), 0); ?></td>
				<td><?php echo number_format(round($bmr - $user['deficit'], 2), 0); ?></td>
			</tr>
		</tbody>
	</table>
</div>