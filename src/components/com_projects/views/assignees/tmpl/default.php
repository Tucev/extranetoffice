<?php //PHPFrame_HTML::dragAndDrop(); ?>
		
		<script type="text/javascript">
			$(document).ready(function() {
				// there's the container for draggables container and droppable area
				var $draggables_container = $('#draggables_container'), $droppable_area = $('#droppable_area');

				// let the items be draggable
				$('li',$draggables_container).draggable({
					cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
					revert: 'invalid', // when not dropped, the item will revert back to its initial position
					helper: 'clone',
					cursor: 'move'
				});

				// let the droppable_area be droppable, accepting the draggables_container items
				$droppable_area.droppable({
					accept: '#draggables_container > li',
					activeClass: 'ui-state-highlight',
					drop: function(ev, ui) {
						moveToDroppableArea(ui.draggable);
					}
				});

				// let the draggables_container be droppable as well, accepting items from the droppable_area
				$draggables_container.droppable({
					accept: '#droppable_area li',
					activeClass: 'custom-state-active',
					drop: function(ev, ui) {
						moveToDraggablesContainer(ui.draggable);
					}
				});

				// image deletion function
				var recycle_icon = '<a href="link/to/recycle/script/when/we/have/js/off" title="Unselect" class="ui-icon ui-icon-refresh">Unselect</a>';
				function moveToDroppableArea($item) {
					// Add item to hidden input holding selected ids
					updateSelectedInput($item);
					
					$item.fadeOut(function() {
						var $list = $('ul',$droppable_area).length ? $('ul',$droppable_area) : $('<ul class="draggables_container ui-helper-reset"/>').appendTo($droppable_area);

						$item.find('a.ui-icon-droppable_area').remove();
						$item.append(recycle_icon).appendTo($list).fadeIn(function() {
							$item.animate({ width: '40px' }).find('img').animate({ height: '50px' });
						});
					});
				}

				// image recycle function
				var droppable_area_icon = '<a href="link/to/droppable_area/script/when/we/have/js/off" title="Move to droppable area" class="ui-icon ui-icon-droppable_area">Move to droppable area</a>';
				function moveToDraggablesContainer($item) {
					// Remove item to hidden input holding selected ids
					updateSelectedInput($item, true);
					
					$item.fadeOut(function() {
						$item.find('a.ui-icon-refresh').remove();
						$item.css('width','80px').append(droppable_area_icon).find('img').css('height','100px').end().appendTo($draggables_container).fadeIn();
					});
				}

				// image preview function, demonstrating the ui.dialog used as a modal window
				function viewDetail($link) {
					var src = $link.attr('href');
					var title = $link.siblings('img').attr('alt');
					var $modal = $('img[src$="'+src+'"]');

					if ($modal.length) {
						$modal.dialog('open')
					} else {
						var img = $('<img alt="'+title+'" width="384" height="288" style="display:none;padding: 8px;" />')
							.attr('src',src).appendTo('body');
						setTimeout(function() {
							img.dialog({
									title: title,
									width: 400,
									modal: true
								});
						}, 1);
					}
				}

				function updateSelectedInput($item, remove) {
					var selected_input = document.assigneesform.selected_users;
					var selected_array = selected_input.value.split(",");
					
					if (remove) {
						selected_array.splice($item.attr("id"), 1);
					} 
					else {
						selected_array[$item.attr("id")] = $item.attr("title");
					}

					selected_input.value = selected_array.join();
				}

				// resolve the icons behavior with event delegation
				$('ul.draggables_container > li').click(function(ev) {
					var $item = $(this);
					var $target = $(ev.target);

					if ($target.is('a.ui-icon-droppable_area')) {
						moveToDroppableArea($item);
					} else if ($target.is('a.ui-icon-refresh')) {
						moveToDraggablesContainer($item);
					} else if ($target.is('a.ui-icon-zoomin')) {
						viewDetail($target);
					}

					return false;
				});

				// Move initially selected items to droppable area
				moveToDroppableArea($(".initially-selected"));
			});
		</script>
		
		<div class="demo ui-widget ui-helper-clearfix">

			<ul id="draggables_container" class="draggables_container ui-helper-reset ui-helper-clearfix">
				
				<?php $count=0; ?>
				<?php if (is_array($data['selected_users']) && count($data['selected_users']) > 0) : ?>
				<?php foreach ($data['selected_users'] as $selected_user) : ?>
				<li id="<?php echo $count; ?>" title="<?php echo $selected_user->userid; ?>" class="ui-widget-content ui-corner-tr initially-selected">
					<h5 class="ui-widget-header"><?php echo PHPFrame_User_Helper::fullname_format($selected_user->firstname, $selected_user->lastname); ?></h5>
					<img src="<?php echo config::UPLOAD_DIR; ?>/users/<?php echo $selected_user->photo; ?>" alt="<?php echo $selected_user->firstname." ".$selected_user->lastname; ?>" />
					<a href="link/to/droppable_area/script/when/we/have/js/off" title="Move to droppable area" class="ui-icon ui-icon-droppable_area">Move to droppable area</a>
					<!-- 
					<a href="images/high_tatras.jpg" title="View larger image" class="ui-icon ui-icon-zoomin">View larger</a>
					 -->
				</li>
				<?php $count++; ?>
				<?php endforeach; ?>
				<?php endif; ?>
				
				<?php if (is_array($data['unselected_users']) && count($data['unselected_users']) > 0) : ?>
				<?php foreach ($data['unselected_users'] as $unselected_user) : ?>
				<li id="<?php echo $count; ?>" title="<?php echo $unselected_user->userid; ?>" class="ui-widget-content ui-corner-tr">
					<h5 class="ui-widget-header"><?php echo PHPFrame_User_Helper::fullname_format($unselected_user->firstname, $unselected_user->lastname); ?></h5>
					<img src="<?php echo config::UPLOAD_DIR; ?>/users/<?php echo $unselected_user->photo; ?>" alt="<?php echo $unselected_user->firstname." ".$unselected_user->lastname; ?>" />
					<a href="link/to/droppable_area/script/when/we/have/js/off" title="Move to droppable area" class="ui-icon ui-icon-droppable_area">Move to droppable area</a>
					<!-- 
					<a href="images/high_tatras.jpg" title="View larger image" class="ui-icon ui-icon-zoomin">View larger</a>
					 -->
				</li>
				<?php $count++; ?>
				<?php endforeach; ?>
				<?php endif; ?>
				
			</ul>

			<div id="droppable_area" class="ui-widget-content ui-state-default">
				<h4 class="ui-widget-header">Selected</h4>
			</div>

		</div>
		
		<form name="assigneesform" id="assigneesform" method="get">
			<input type="hidden" name="selected_users" value="" />
			<input type="hidden" name="component" value="com_projects" />
			<input type="hidden" name="action" value="save_assignees" />
			<input type="hidden" name="projectid" value="" />
			<input type="hidden" name="tool" value="" />
			<input type="hidden" name="itemid" value="" />
		</form>
		