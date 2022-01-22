<?php $phlo_info = (array)$this->phlo[0]; ?>
<?php for ($ord = 0; $ord < count($this->order); $ord++) : // loop through order ?>
			<?php $order = (array)$this->order[$ord]; ?>
			<?php for ($par_item = 0; $par_item < count($this->parent_obj); $par_item++) : // parent items ?>
				<?php $parent = (array)$this->parent_obj[$par_item]; ?>
				<?php if($parent['id']==$order['itemid']) : ?>
						
				<!-- list item <?php echo $parent['id']; ?> -->
				<li id="parent_<?php echo $parent['id']; ?>" class="branch<?php if($parent['haschild']): ?> collapsed<?php endif; ?> no-nesting">
					
					<!-- parent content -->
					<div id="title_wrap_<?php echo $parent['id']; ?>" class="parent-title">
						<input type="text" id="phlotitle_<?php echo $parent['id']; ?>" value="<?php echo $parent['title']; ?>" class="title" />
						<img src="<?php echo URL; ?>public/img/move.png" class="move" title="move this box" alt="move this item" />
						<img src="<?php echo URL; ?>public/img/remove.png" class="remove" title="remove this box" alt="remove this box" />
						<img src="<?php echo URL; ?>public/img/add.png" class="add" title="add a new child" alt="add a new child" />
						<span class="man"><span></span></span>
					</div>
					
					<!-- child content -->
					<!-- note different templates to be called here -->
					<?php if($parent['haschild']): ?>
						<?php for ($child_item = 0; $child_item < count($this->child_obj); $child_item++) : // child items ?>
						<?php $child = (array)$this->child_obj[$child_item]; ?>
							<?php if($child['parent']==$parent['id']) : ?>
								<!-- // id, pid, parent, title, fieldtype, selectoptions, response, weighting -->
								<ol id="phloitem_<?php echo $child_item; ?>" class="phloitem">					
									<li id="child_<?php echo $child['id']; ?>" class="phlo-branch phlo-collapsed phlo-no-nesting" style="display:list-item;">
										<div class="subtitle-container">
											<input type="text" id="phlosubtitle_1" value="Sub Item 1.1" class="subtitle" />
											<img src="<?php echo URL; ?>public/img/s_move.png" class="move" style="margin-left:5px;" title="move this werkphlo item" alt="move this item" />
											<img src="<?php echo URL; ?>public/img/s_remove.png" class="remove" title="remove this item" alt="remove this item" />
											<img src="<?php echo URL; ?>public/img/s_manage.png" class="answer" title="manage this item's content" alt="manage this item's content" />
											<div class="hidden-content" style="display:none;">
												<div class="border"></div>						
												<div class="container">
												<div class="row">
													<div class="column mr">
														<label for="weighting_1">Item weighting</label>
														<input type="text" name="phloitem_1[weighting]" id="weighting_1" value="" />
													</div>
													<div class="column mr">
														<label for="fieldtype_1">Field type</label>
														<input type="text" name="phloitem_1[fieldtype]" id="fieldtype_1" value=""/>
													</div>
													<div class="column">
														<label for="selectoptions_1">Select options</label>
														<input type="text" name="phloitem_1[selectoptions]" id="selectoptions_1" value="" />
													</div>
									    		</div>
											</div>
											<div class="container">
												<div class="row">
											    	<div class="column-3">
											    		<label for="response_1">Survey question response</label>
											    		<textarea name="phloitem_1[response]" id="response_1" class="response"></textarea>
											    	</div>
									    		</div>
											</div>
										</div>									
									</div>
								</li>
							</ol>									
							<?php endif; ?>
						<?php endfor; ?>
					<?php endif; ?>
											
				</li>						
						
				<?php endif; ?>
			<?php endfor; ?>
		<?php endfor; ?>