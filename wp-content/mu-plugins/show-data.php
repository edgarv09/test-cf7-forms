<?php
/**
 * Template Name: Show Data
 * Template Post Type: post, page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<div class="wrap">
			<?php
			// Start the Loop.
			global $wpdb;
			// this adds the prefix which is set by the user upon instillation of wordpress
			$table_name = $wpdb->prefix . "cf7_vdata_entry";
			// this will get the data from your table
			//$retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name limit 1" );
			//
			$form_id = $_GET['form_id'];
			$data_id = $_GET['data_id'];

// 						$retrieve_data = $wpdb->get_results( "
// SELECT JSON_OBJECTAGG(name, value) as json
// FROM $table_name
// where cf7_id = 25 and data_id = 9" );
//
//
						$retrieve_data = $wpdb->get_results( "
SELECT JSON_OBJECTAGG(name, value) as json
FROM $table_name
where cf7_id = $form_id and data_id = $data_id" );
			?>
			<ul>
				<?php	foreach ($retrieve_data as $retrieved_data): ?>
					<?php
					$attr = get_object_vars ( $retrieved_data)
					?>
					<?php $json = json_decode($retrieved_data->json,true) ?>
					<li>get type json <?php echo gettype($json);?></li>
					<li>var dump json <?php echo var_dump($json);?></li>
					<li><?php echo $json['city'];?></li>


					<li><?php echo gettype($retrieved_data);?></li>
					<li><?php echo $attr;?></li>
					<li><?php echo $attr["json"];?></li>
					<li><?php echo var_dump($attr);?></li>





				<?php endforeach; ?>
			</ul>
</div><!-- .wrap -->

<?php get_footer(); ?>
