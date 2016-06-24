<?php
$canada_state = array('AB' => 'AB', 'BC' => 'BC', 'LB' => 'LB', 'MB' => 'MB', 'NB' => 'NB', 'NF' => 'NF', 'NS' => 'NS', 'NT' => 'NT', 'NU' => 'NU', 'ON' => 'ON', 'PE' => 'PE', 'PQ' => 'PQ', 'QB' => 'QB', 'QC' => 'QC', 'SK' => 'SK', 'YT' => 'YT');
$us_states = array('AK' => 'AK', 'AL' => 'AL', 'AP' => 'AP', 'AR' => 'AR', 'AS' => 'AS', 'AZ' => 'AZ', 'CA' => 'CA', 'CO' => 'CO', 'CT' => 'CT', 'DC' => 'DC', 'DE' => 'DE', 'FL' => 'FL', 'FM' => 'FM', 'GA' => 'GA', 'GS' => 'GS', 'GU' => 'GU', 'HI' => 'HI', 'IA' => 'IA', 'ID' => 'ID', 'IL' => 'IL', 'IN' => 'IN', 'KS' => 'KS', 'KY' => 'KY', 'LA' => 'LA', 'MA' => 'MA', 'MD' => 'MD', 'ME' => 'ME', 'MH' => 'MH', 'MI' => 'MI', 'MN' => 'MN', 'MO' => 'MO', 'MP' => 'MP', 'MS' => 'MS', 'MT' => 'MT', 'NC' => 'NC', 'ND' => 'ND', 'NE' => 'NE', 'NH' => 'NH', 'NJ' => 'NJ', 'NM' => 'NM', 'NV' => 'NV', 'NY' => 'NY', 'OH' => 'OH', 'OK' => 'OK', 'OR' => 'OR', 'PA' => 'PA', 'PR' => 'PR', 'PW' => 'PW', 'RI' => 'RI', 'SC' => 'SC', 'SD' => 'SD', 'TN' => 'TN', 'TX' => 'TX', 'UT' => 'UT', 'VA' => 'VA', 'VI' => 'VI', 'VT' => 'VT');

$country = $_REQUEST['country'];
if ($country == "Canada") {
        $state = $canada_state;
} else {
        $state = $us_states;
}

foreach ($state as $c => $cs) {
        $var .='<option value="'.$c.'">'.$cs.'</option>';
}
                                    
echo $var;