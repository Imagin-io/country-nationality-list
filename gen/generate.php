<?php

require_once('parsecsv.lib.php');
$csv = new parseCSV();
$csv->auto("master-list.csv");

generateCSV();
generateJSON($csv);
generateMySql($csv);
generatePHP($csv);

/**
 * Generate CSV File
 * -------------------
 * Simply copy master CSV file
 *
 * @return bolean
 **/
function generateCSV()
{
    if (!is_writable('../countries.csv')) die(__FUNCTION__ . ': I can haz write permissions pls?');
    return copy('master-list.csv', '../countries.csv');
}

/**
 * Generate JSON File
 * -------------------
 * 
 *
 * @return bolean
 **/
function generateJSON($csv)
{
    if (!is_writable('../countries.json')) die(__FUNCTION__ . ': I can haz write permissions pls?');
    $out = json_encode($csv->data, JSON_PRETTY_PRINT);
    return file_put_contents('../countries.json', $out);
}

/**
 * Generate MySQL Table
 * --------------------
 * @return boolean
 **/
function generateMySql($csv)
{
    if (!is_writable('../countries.sql')) die(__FUNCTION__ . ': I can haz write permissions pls?');
    $sql = '
DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `num_code` int(3) NOT NULL DEFAULT \'0\',
  `alpha_2_code` varchar(2) DEFAULT NULL,
  `alpha_3_code` varchar(3) DEFAULT NULL,
  `en_short_name` varchar(52) DEFAULT NULL,
  `nationality` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`num_code`),
  UNIQUE KEY `alpha_2_code` (`alpha_2_code`),
  UNIQUE KEY `alpha_3_code` (`alpha_3_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `countries` (`num_code`, `alpha_2_code`, `alpha_3_code`, `en_short_name`, `nationality`) VALUES';
    foreach ($csv->data as $d) {
        $sql .= "\n(\"$d[num_code]\", \"$d[alpha_2_code]\", \"$d[alpha_3_code]\", \"$d[en_short_name]\", \"$d[nationality]\"),"; 
    }
    $sql = trim($sql, ',');

    return file_put_contents('../countries.sql', $sql);
}


/**
 * Generate PHP File
 * -------------------
 * 
 *
 * @return bolean
 **/
function generatePHP($csv)
{
    if (!is_writable('../countries.php')) die(__FUNCTION__ . ': I can haz write permissions pls?');
    $out = '<?php
$countries = array();
';
    foreach ($csv->data as $d) {
      $out .= '
$countries[] = array(
  "num_code" => "' . $d['num_code'] . '",
  "alpha_2_code" => "' . $d['alpha_2_code'] . '",
  "alpha_3_code" => "' . $d['alpha_3_code'] . '",
  "en_short_name" => "' . $d['en_short_name'] . '",
  "nationality" => "' . $d['nationality'] . '",
  );
';
    }
    return file_put_contents('../countries.php', $out);
}
