<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
	ob_start();	
	$count = 1;
	$key = 'key_replace'; 
	$name = 'name_replace'; 
	$field = array();
	$settings = array( 'conditional_logic' => 0, 'conditional_logic_action' => 'show', 'conditional_logic_operator' => 'and' );
	include( 'settings-field-advanced.php' );
	$content = ob_get_clean();
	//$content = str_replace( PHP_EOL, "';" . PHP_EOL . "advanced_tab_content +='", $content );
    $content = json_encode($content);
?>

var advanced_tab_content = <?php echo $content; ?>;
var advanced_tab_content2 = advanced_tab_content;
advanced_tab_content2 = advanced_tab_content2.replace( 'key_replace', field_section );
advanced_tab_content2 = advanced_tab_content2.replace( 'name_replace', field_slug );
while ( advanced_tab_content2 != advanced_tab_content ) {
	advanced_tab_content = advanced_tab_content2; 
	advanced_tab_content2 = advanced_tab_content2.replace( 'key_replace', field_section );
	advanced_tab_content2 = advanced_tab_content2.replace( 'name_replace', field_slug );
}
html += advanced_tab_content;

