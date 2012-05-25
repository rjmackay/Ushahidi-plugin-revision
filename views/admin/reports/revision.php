<?php 
/**
 * Reports revision view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Robbie Mackay
 * @package    Revision
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
			<div class="bg">
				<h2>
					<?php admin::reports_subtabs(""); ?>
				</h2>
				
				<ul>
				<?php foreach ($revisions as $revision)
				{
					//echo "<li>$revision</li>";
					echo "<li>";
					echo isset($revision->user_id) ? Kohana::lang('ui_admin.edited_by')." ".$revision->user->name.". " : '';
					if ($revision->changed_data())
					{
						Kohana::lang('revision.changed_fields').": <br />";
						echo "<ul>";
						foreach ($revision->changed_data() as $field => $value)
						{
							if (!is_array($value))
							{
								echo "<li>$field to $value</li>";
							}
							else
							{
								switch ($field)
								{
									case 'category':
										echo "<li>$field to "; 
										$full_data = $revision->data();
										foreach ($full_data['category'] as $f => $v)
										{
											echo $v['category_title'].", ";
										}
										echo "</li>";
										break;
									case 'media':
										echo "<li>$field to "; 
										foreach ($value as $f => $v)
										{
											echo $v['media_link'];
										}
										echo "</li>";
										break;
									case 'form_response':
										foreach ($value as $f => $v)
										{
											$form_field = ORM::factory('form_field',$v['form_field_id']);
											echo "<li>$form_field->field_name to $v[form_response]</li>";
										}
										break;
									default:
										foreach ($value as $f => $v)
										{
											echo "<li>$field:$f to $v</li>";
										}
								}
							}
						}
						echo "</ul>";
					}
					echo "(" .$revision->time. ")";
					echo "</li>";
				}
				?>
				</ul>
				
			</div>
