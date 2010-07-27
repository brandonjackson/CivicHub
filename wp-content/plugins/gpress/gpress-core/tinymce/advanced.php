<?php

global $tppo;
$default_map_type = $tppo->get_tppo('default_map_type', 'blogs');
$default_map_zoom = $tppo->get_tppo('default_map_zoom', 'blogs');

if(empty($default_map_type)) {
	$map_type = 'ROADMAP';
}
if(empty($default_map_zoom)) {
	$map_zoom = '13';
}

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="gpress_meta_sidebar_table">
  <tr>
    <td width="50%">
        <label>Map Type:</label>
        <input type="radio" name="map-type" value="ROADMAP" <?php if($map_type == 'ROADMAP') { ?> checked="checked"<?php } ?> /><span class="input_label">Roadmap</span><br />
        <input type="radio" name="map-type" value="SATELLITE" <?php if($map_type == 'SATELLITE') { ?> checked="checked"<?php } ?> /><span class="input_label">Satellite</span><br />
        <input type="radio" name="map-type" value="HYBRID" <?php if($map_type == 'HYBRID') { ?> checked="checked"<?php } ?> /><span class="input_label">Hybrid</span><br />
        <input type="radio" name="map-type" value="TERRAIN" <?php if($map_type == 'TERRAIN') { ?> checked="checked"<?php } ?> /><span class="input_label">Terrain</span><br />
    </td>
    <td width="10px" class="divider">&nbsp;</td>
    <td width="50%" align="right">
        <label>Zoom Level:</label>
        <input type="radio" name="map-zoom" value="18" <?php if($map_zoom == '18') { ?> checked="checked"<?php } ?> style="float:right" /><span class="input_label" style="float:right">Close-Up</span><br />
        <input type="radio" name="map-zoom" value="13" <?php if($map_zoom == '13') { ?> checked="checked"<?php } ?> style="float:right" /><span class="input_label" style="float:right">Nearby</span><br />
        <input type="radio" name="map-zoom" value="10" <?php if($map_zoom == '10') { ?> checked="checked"<?php } ?> style="float:right" /><span class="input_label" style="float:right">Cities</span><br />
        <input type="radio" name="map-zoom" value="5" <?php if($map_zoom == '5') { ?> checked="checked"<?php } ?> style="float:right" /><span class="input_label" style="float:right">Countries</span><br />
    </td>
  </tr>
</table>