<?php
/**
 * @package     RedShop
 * @subpackage  Report
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

echo "<html lang=\"en\">
		<head>
		<meta charset=\"utf-8\">
		<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
		<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css\">
		<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css\">
		<script src=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js\"></script>
		<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js\"></script>
		</html>";
$attributes = array("name", "tests", "assertions", "failures", "errors");

if (file_exists('../logs/junit.xml'))
{
	$xml = simplexml_load_file('../logs/junit.xml');
	echo "<table class=\"table table-striped\">";
	echo "<thead>";
	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Tests</th>";
	echo "<th>Assertions</th>";
	echo "<th>Failures</th>";
	echo "<th>Errors</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";

	for ($i = 0; $i < $xml->testsuite[0]->children()->count(); $i++)
	{
		echo "<tr>";

		foreach ($xml->testsuite[0]->testsuite[$i]->attributes() as $a => $b)
		{
			if (in_array($a, $attributes))
			{
				echo "<td>";
				echo $b;
				echo "</td>";
			}
		}

		echo "</tr>";
	}

	echo "</tbody>";
	echo "</table>";
}
else
{
	echo "<h1>";
	echo "Please execute System Test! to view the report";
	echo "</h1>";
	echo "<h4>";
	echo "For more details about how to run system tests please read here";
	echo "<a href=\"https://redweb.atlassian.net/wiki/display/RSBTB/2013/12/06/Running+System+Tests%2C+Automation+Testing+for+RedshopB2B\">";
	echo " System Test";
	echo "</a>";
	echo "</h2>";
}
