<?php

// Version v1.2
// Ce script expérimental réalisé en PHP permet de déterminer la phase de la journée (dont les différentes phases d'aube et de crépuscule)
// L'algorythme est basé sur une étude faite par la National Oceanic and Atmospheric Administration

// LES VARIABLES DE LA BOX EEDOMUS:
// [VAR1] = Latitude (+ => N)
// [VAR2] = Longitude (+ => E)

// Les latitude et longitude doivent contenir des "." (PAS DE VIRGULE)
// EXEMPLE: Latitude = 48.858346
//          Longitude = 2.294496
// OU
//
// EXEMPLE: Latitude = 48.387942
//          Longitude = -4.484993

// EXEMPLE APPEL DE SCRIPT avec variables: http://localhost/script/?exec=aube_crepuscule.php&latitude=[VAR1]&longitude=[VAR2]

// LE RESULTAT EST SOUS FORME XML
// XPATH Phase de la journée: /Data/Soleil/Day_Phase

// 0 = Nuit
// 1 = Aube astronomique
// 2 = Aube nautique
// 3 = Aube civile
// 4 = Jour
// 5 = Crépuscule civil
// 6 = Crépuscule nautique
// 7 = Crépuscule astronomique

//--------------------------------------------------------------

// Stocker les variables passées en argument
$ma_latitude = getArg('latitude');
$ma_longitude = getArg('longitude');

$time_zone = substr(date('O'), -4, 4);
$time_zone = $time_zone / 100;

$diff_jour = strtotime(date('Y')."-".date('m')."-".date('d')." 00:00:00");

//--------------------------------------------------------------

$heure_secondes = date('H') * 3600;
$minutes_secondes = date('i') * 60;
$heure_secondes = $heure_secondes + $minutes_secondes + date('s');

$Time_past_local_midnight = $heure_secondes/ 86400;

$Julian_Day =  $diff_jour / 86400 + 2440587.5 + $Time_past_local_midnight;

$Julian_Century = $Julian_Day - 2451545;
$Julian_Century = $Julian_Century / 36525;

$Geom_Mean_Long_Sun_deg_1 = $Julian_Century * '0.0003032';
$Geom_Mean_Long_Sun_deg_1 = '36000.76983' + $Geom_Mean_Long_Sun_deg_1;
$Geom_Mean_Long_Sun_deg_1 = $Julian_Century * $Geom_Mean_Long_Sun_deg_1;
$Geom_Mean_Long_Sun_deg = fmod('280.46646' + $Geom_Mean_Long_Sun_deg_1,360);

$Geom_Mean_Anom_Sun_deg = '0.0001537' * $Julian_Century;
$Geom_Mean_Anom_Sun_deg = '35999.05029' - $Geom_Mean_Anom_Sun_deg;
$Geom_Mean_Anom_Sun_deg = $Julian_Century * $Geom_Mean_Anom_Sun_deg;
$Geom_Mean_Anom_Sun_deg = '357.52911' + $Geom_Mean_Anom_Sun_deg;

$Eccent_Earth_Orbit = '0.0000001267'*$Julian_Century;
$Eccent_Earth_Orbit = '0.000042037' + $Eccent_Earth_Orbit;
$Eccent_Earth_Orbit = $Julian_Century * $Eccent_Earth_Orbit;
$Eccent_Earth_Orbit = '0.016708634' - $Eccent_Earth_Orbit;

$Sun_Eq_of_Ctr_1 = deg2rad(3 * $Geom_Mean_Anom_Sun_deg);
$Sun_Eq_of_Ctr_1 = sin($Sun_Eq_of_Ctr_1);
$Sun_Eq_of_Ctr_1 = $Sun_Eq_of_Ctr_1 * '0.000289';
$Sun_Eq_of_Ctr_2 = '0.000101' * $Julian_Century;
$Sun_Eq_of_Ctr_2 = '0.019993' - $Sun_Eq_of_Ctr_2;
$Sun_Eq_of_Ctr_3 = deg2rad(2* $Geom_Mean_Anom_Sun_deg);
$Sun_Eq_of_Ctr_3 = sin($Sun_Eq_of_Ctr_3);
$Sun_Eq_of_Ctr = $Sun_Eq_of_Ctr_2 * $Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr = $Sun_Eq_of_Ctr + $Sun_Eq_of_Ctr_1;
$Sun_Eq_of_Ctr_3 = '0.000014' * $Julian_Century;
$Sun_Eq_of_Ctr_3 = $Sun_Eq_of_Ctr_3 + '0.004817';
$Sun_Eq_of_Ctr_3 = $Julian_Century * $Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr_3 = '1.914602' - $Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr_2 = deg2rad( $Geom_Mean_Anom_Sun_deg);
$Sun_Eq_of_Ctr_2 = sin($Sun_Eq_of_Ctr_2);
$Sun_Eq_of_Ctr_2 = $Sun_Eq_of_Ctr_2*$Sun_Eq_of_Ctr_3;
$Sun_Eq_of_Ctr = $Sun_Eq_of_Ctr_2 + $Sun_Eq_of_Ctr;

$Sun_True_Long_deg = $Geom_Mean_Long_Sun_deg + $Sun_Eq_of_Ctr;

$Sun_True_Anom_deg = $Geom_Mean_Anom_Sun_deg + $Sun_Eq_of_Ctr;

$Sun_Rad_Vector_AUs_1 = deg2rad($Sun_True_Anom_deg);
$Sun_Rad_Vector_AUs_1 = cos($Sun_Rad_Vector_AUs_1);
$Sun_Rad_Vector_AUs_1 = $Eccent_Earth_Orbit * $Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs = 1 + $Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs_1  = $Eccent_Earth_Orbit*$Eccent_Earth_Orbit;
$Sun_Rad_Vector_AUs_1 = 1-$Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs_1 = '1.000001018'*$Sun_Rad_Vector_AUs_1;
$Sun_Rad_Vector_AUs = $Sun_Rad_Vector_AUs_1 / $Sun_Rad_Vector_AUs;

$Sun_App_Long_deg_1 = '1934.136'*$Julian_Century;
$Sun_App_Long_deg_1 = '125.04'-$Sun_App_Long_deg_1;
$Sun_App_Long_deg_1 = deg2rad($Sun_App_Long_deg_1);
$Sun_App_Long_deg_1 = sin($Sun_App_Long_deg_1);
$Sun_App_Long_deg_1 = '0.00478'*$Sun_App_Long_deg_1;
$Sun_App_Long_deg = $Sun_True_Long_deg - '0.00569'-$Sun_App_Long_deg_1;

$Mean_Obliq_Ecliptic_deg = $Julian_Century * '0.001813';
$Mean_Obliq_Ecliptic_deg = '0.00059'- $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Julian_Century * $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = '46.815' + $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Julian_Century * $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = '21.448' - $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Mean_Obliq_Ecliptic_deg / 60;
$Mean_Obliq_Ecliptic_deg = '26' + $Mean_Obliq_Ecliptic_deg;
$Mean_Obliq_Ecliptic_deg = $Mean_Obliq_Ecliptic_deg / 60;
$Mean_Obliq_Ecliptic_deg = 23 + $Mean_Obliq_Ecliptic_deg;

$Obliq_Corr_deg = '1934.136'*$Julian_Century;
$Obliq_Corr_deg = '125.04'-$Obliq_Corr_deg;
$Obliq_Corr_deg = deg2rad($Obliq_Corr_deg);
$Obliq_Corr_deg = cos($Obliq_Corr_deg);
$Obliq_Corr_deg = $Obliq_Corr_deg*'0.00256';
$Obliq_Corr_deg = $Mean_Obliq_Ecliptic_deg + $Obliq_Corr_deg;

$Sun_Declin_deg_1 = deg2rad($Sun_App_Long_deg);
$Sun_Declin_deg_1 = sin($Sun_Declin_deg_1);
$Sun_Declin_deg_2 = deg2rad($Obliq_Corr_deg);
$Sun_Declin_deg_2 = sin($Sun_Declin_deg_2);
$Sun_Declin_deg = asin($Sun_Declin_deg_1*$Sun_Declin_deg_2);
$Sun_Declin_deg = rad2deg($Sun_Declin_deg);

$SunRt_Ascen_deg_1 = deg2rad($Sun_App_Long_deg);
$SunRt_Ascen_deg_1 = sin($SunRt_Ascen_deg_1);
$SunRt_Ascen_deg_2 = deg2rad($Obliq_Corr_deg);
$SunRt_Ascen_deg_2 = cos($SunRt_Ascen_deg_2);
$SunRt_Ascen_deg_1 = $SunRt_Ascen_deg_2 * $SunRt_Ascen_deg_1;
$SunRt_Ascen_deg_2 = deg2rad($Sun_App_Long_deg);
$SunRt_Ascen_deg_2 = cos($SunRt_Ascen_deg_2);
$SunRt_Ascen_deg = atan2($SunRt_Ascen_deg_1, $SunRt_Ascen_deg_2);
$SunRt_Ascen_deg = rad2deg($SunRt_Ascen_deg);

$y_1 = $Obliq_Corr_deg/2;
$y_1 = deg2rad($y_1);
$y_1 = tan($y_1);
$y = $Obliq_Corr_deg/2;
$y = deg2rad($y);
$y = tan($y);
$y = $y * $y_1;

$Eq_of_Time_minutes_1 = deg2rad($Geom_Mean_Anom_Sun_deg);
$Eq_of_Time_minutes_1 = 2 * $Eq_of_Time_minutes_1;
$Eq_of_Time_minutes_1 = sin($Eq_of_Time_minutes_1);
$Eq_of_Time_minutes_1 = '1.25' * $Eccent_Earth_Orbit * $Eccent_Earth_Orbit * $Eq_of_Time_minutes_1;
$Eq_of_Time_minutes_2 = deg2rad($Geom_Mean_Long_Sun_deg);
$Eq_of_Time_minutes_2 = 4 * $Eq_of_Time_minutes_2;
$Eq_of_Time_minutes_2 = sin($Eq_of_Time_minutes_2);
$Eq_of_Time_minutes_2 = '0.5' * $y * $y * $Eq_of_Time_minutes_2;
$Eq_of_Time_minutes_3 = deg2rad($Geom_Mean_Long_Sun_deg);
$Eq_of_Time_minutes_3 = 2 * $Eq_of_Time_minutes_3;
$Eq_of_Time_minutes_3 = cos($Eq_of_Time_minutes_3);
$Eq_of_Time_minutes_4 = deg2rad($Geom_Mean_Anom_Sun_deg);
$Eq_of_Time_minutes_4 = sin($Eq_of_Time_minutes_4);
$Eq_of_Time_minutes_4 = 4 * $Eccent_Earth_Orbit * $y * $Eq_of_Time_minutes_4 * $Eq_of_Time_minutes_3;
$Eq_of_Time_minutes_5 = deg2rad($Geom_Mean_Anom_Sun_deg);
$Eq_of_Time_minutes_5 = sin($Eq_of_Time_minutes_5);
$Eq_of_Time_minutes_5 = 2 * $Eccent_Earth_Orbit * $Eq_of_Time_minutes_5;
$Eq_of_Time_minutes_6 = deg2rad($Geom_Mean_Long_Sun_deg);
$Eq_of_Time_minutes_6 = 2 * $Eq_of_Time_minutes_6;
$Eq_of_Time_minutes_6 = sin($Eq_of_Time_minutes_6);
$Eq_of_Time_minutes_6 = $y * $Eq_of_Time_minutes_6;

$Eq_of_Time_minutes = $Eq_of_Time_minutes_6 - $Eq_of_Time_minutes_5 + $Eq_of_Time_minutes_4 - $Eq_of_Time_minutes_2 - $Eq_of_Time_minutes_1;
$Eq_of_Time_minutes = 4 * rad2deg($Eq_of_Time_minutes);

$Solar_Noon_LST = $time_zone * 60;
$Solar_Noon_LST_1 = 4 * $ma_longitude;
$Solar_Noon_LST = 720 - $Solar_Noon_LST_1 - $Eq_of_Time_minutes + $Solar_Noon_LST;
$Solar_Noon_Hours = floor($Solar_Noon_LST / 60);
$Solar_Noon_Minutes = $Solar_Noon_LST % 60;
$Solar_Noon_LST = $Solar_Noon_LST / 1440;

$HA_Sunrise_deg = sdk_hasunrisedeg($Sun_Declin_deg, $ma_latitude, 90.833);
$Sunrise_Time_LST = sdk_sunrisetimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Sunrise_Time_Hours = floor($Sunrise_Time_LST * 1440 / 60);
$Sunrise_Time_Minutes = ($Sunrise_Time_LST * 1440) % 60;
$Sunset_Time_LST = sdk_sunsettimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Sunset_Time_Hours = floor($Sunset_Time_LST * 1440 / 60);
$Sunset_Time_Minutes = ($Sunset_Time_LST * 1440) % 60;

$HA_Sunrise_deg = sdk_hasunrisedeg($Sun_Declin_deg, $ma_latitude, 96);
$Civil_Start_Time_LST = sdk_sunrisetimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Civil_Start_Time_Hours = floor($Civil_Start_Time_LST * 1440 / 60);
$Civil_Start_Time_Minutes = ($Civil_Start_Time_LST * 1440) % 60;
$Civil_End_Time_LST = sdk_sunsettimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Civil_End_Time_Hours = floor($Civil_End_Time_LST * 1440 / 60);
$Civil_End_Time_Minutes = ($Civil_End_Time_LST * 1440) % 60;
   
$HA_Sunrise_deg = sdk_hasunrisedeg($Sun_Declin_deg, $ma_latitude, 102);
$Nautical_Start_Time_LST = sdk_sunrisetimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Nautical_Start_Time_Hours = floor($Nautical_Start_Time_LST * 1440 / 60);
$Nautical_Start_Time_Minutes = ($Nautical_Start_Time_LST * 1440) % 60;
$Nautical_End_Time_LST = sdk_sunsettimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Nautical_End_Time_Hours = floor($Nautical_End_Time_LST * 1440 / 60);
$Nautical_End_Time_Minutes = ($Nautical_End_Time_LST * 1440) % 60;

$HA_Sunrise_deg = sdk_hasunrisedeg($Sun_Declin_deg, $ma_latitude, 108);
$Astro_Time_OK = (sprintf("%s", $HA_Sunrise_deg) == 'NAN' ? FALSE : TRUE);
$Astro_Start_Time_LST = sdk_sunrisetimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Astro_Start_Time_Hours = floor($Astro_Start_Time_LST * 1440 / 60);
$Astro_Start_Time_Minutes = ($Astro_Start_Time_LST * 1440) % 60;
$Astro_End_Time_LST = sdk_sunsettimelst($HA_Sunrise_deg, $Solar_Noon_LST);
$Astro_End_Time_Hours = floor($Astro_End_Time_LST * 1440 / 60);
$Astro_End_Time_Minutes = ($Astro_End_Time_LST * 1440) % 60;

// Associe la phase de la journée à l'heure
if ($Astro_Time_OK && $Time_past_local_midnight < $Astro_Start_Time_LST )
   { $Day_phase = 0;}      // Nuit

if ($Time_past_local_midnight >= $Astro_Start_Time_LST )
   { $Day_phase = 1;}      // Aube Astronomique

if ($Time_past_local_midnight >= $Nautical_Start_Time_LST )
   { $Day_phase = 2;}      // Aube Nautique
   
if ($Time_past_local_midnight >= $Civil_Start_Time_LST )
   { $Day_phase = 3;}      // Aube Civile

if ($Time_past_local_midnight >= $Sunrise_Time_LST )
   { $Day_phase = 4;}      // Jour

if ($Time_past_local_midnight > $Sunset_Time_LST )
   { $Day_phase = 5;}      // Crépuscule Civil

if ($Time_past_local_midnight > $Civil_End_Time_LST )
   { $Day_phase = 6;}      // Crépuscule Nautique

if ($Time_past_local_midnight > $Nautical_End_Time_LST )
   { $Day_phase = 7;}      // Crépuscule Astronomique

if ($Astro_Time_OK && $Time_past_local_midnight > $Astro_End_Time_LST )
   { $Day_phase = 0;}      // Nuit

$content_type = 'text/xml';
sdk_header($content_type);

echo "<Data>";
echo "<Parametres>";
echo "<Latitude>".$ma_latitude."</Latitude>";
echo "<Longitude>".$ma_longitude."</Longitude>";
echo "</Parametres>";
echo "<Soleil>";
if ($Astro_Time_OK)
	printf("<Astro_Start_Time>%02dH%02d</Astro_Start_Time>", $Astro_Start_Time_Hours, $Astro_Start_Time_Minutes);
printf("<Nautical_Start_Time>%02dH%02d</Nautical_Start_Time>", $Nautical_Start_Time_Hours, $Nautical_Start_Time_Minutes);
printf("<Civil_Start_Time>%02dH%02d</Civil_Start_Time>", $Civil_Start_Time_Hours, $Civil_Start_Time_Minutes);
printf("<Sunrise_Time>%02dH%02d</Sunrise_Time>", $Sunrise_Time_Hours, $Sunrise_Time_Minutes);
printf("<Solar_Noon>%02dH%02d</Solar_Noon>", $Solar_Noon_Hours, $Solar_Noon_Minutes);
printf("<Sunset_Time>%02dH%02d</Sunset_Time>", $Sunset_Time_Hours, $Sunset_Time_Minutes);
printf("<Civil_End_Time>%02dH%02d</Civil_End_Time>", $Civil_End_Time_Hours, $Civil_End_Time_Minutes);
printf("<Nautical_End_Time>%02dH%02d</Nautical_End_Time>", $Nautical_End_Time_Hours, $Nautical_End_Time_Minutes);
if ($Astro_Time_OK)
	printf("<Astro_End_Time>%02dH%02d</Astro_End_Time>", $Astro_End_Time_Hours, $Astro_End_Time_Minutes);
echo "<Day_Phase>".$Day_phase."</Day_Phase>";
echo "</Soleil>";
echo "</Data>";

function sdk_hasunrisedeg ($Sun_Declin_deg, $ma_latitude, $zenith)
{
    $HA_Sunrise_deg_1 = deg2rad($Sun_Declin_deg);
    $HA_Sunrise_deg_1 = tan($HA_Sunrise_deg_1);
    $HA_Sunrise_deg_2 = deg2rad($ma_latitude);
    $HA_Sunrise_deg_2 = tan($HA_Sunrise_deg_2);
    $HA_Sunrise_deg_1 = $HA_Sunrise_deg_1 * $HA_Sunrise_deg_2;
    $HA_Sunrise_deg_2 = deg2rad($Sun_Declin_deg);
    $HA_Sunrise_deg_5 = cos($HA_Sunrise_deg_2);
    $HA_Sunrise_deg_3 = deg2rad($ma_latitude);
    $HA_Sunrise_deg_3 = cos($HA_Sunrise_deg_3);
    $HA_Sunrise_deg_3 = $HA_Sunrise_deg_3 * $HA_Sunrise_deg_5;
    $HA_Sunrise_deg_4 = deg2rad($zenith);
    $HA_Sunrise_deg_4 = cos($HA_Sunrise_deg_4);
    $HA_Sunrise_deg = $HA_Sunrise_deg_4 / $HA_Sunrise_deg_3;
    $HA_Sunrise_deg = $HA_Sunrise_deg - $HA_Sunrise_deg_1;
    $HA_Sunrise_deg = acos($HA_Sunrise_deg);
    $HA_Sunrise_deg = rad2deg($HA_Sunrise_deg);
    return $HA_Sunrise_deg;
}

function sdk_sunrisetimelst($HA_Sunrise_deg, $Solar_Noon_LST)
{
    $Sunrise_Time_LST = $HA_Sunrise_deg*4;
    $Sunrise_Time_LST = $Sunrise_Time_LST / 1440;
    $Sunrise_Time_LST = $Solar_Noon_LST - $Sunrise_Time_LST;
    return $Sunrise_Time_LST;
}

function sdk_sunsettimelst($HA_Sunrise_deg, $Solar_Noon_LST)
{
    $Sunset_Time_LST = $HA_Sunrise_deg*4;
    $Sunset_Time_LST = $Sunset_Time_LST / 1440;
    $Sunset_Time_LST = $Solar_Noon_LST + $Sunset_Time_LST;
    return $Sunset_Time_LST;
}

?>
