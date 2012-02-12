<div class="container list select data">
<?php if (count($rivers)) : ?>
	<div class="controls edit-advanced">
		<div class="row cf">
			<p class="button-go edit-single"><a href="#">Edit River</a></p>
			<p class="button-view edit-multiple"><a href="<?php echo URL::site()?>dashboard/edit_multiple">Edit multiple</a></p>
			<p class="button-go create-new"><a href="<?php echo URL::site().'river/new'; ?>"><?php echo __('Create new');?></a></p>
		</div>
	</div>
	<?php foreach ($rivers as $river): ?>
	<article class="item cf" id="item_<?php echo $river->id; ?>">
		<div class="content">
			<h1>
			<!--  Namespace the river name if logged in user is not the owner -->
			<?php if ($river->account->user->id != $logged_in_user_id): ?>
			<a href="<?php echo URL::site('user').'/'.$river->account->account_path; ?>">
				<?php echo $river->account->account_path ?>/
			</a>
			<?php endif; ?>
			<a href="<?php echo URL::site().'river/index/'.$river->id; ?>" class="title">
				<?php echo $river->river_name; ?>
			</a>
			</h1>
		</div>
		<div class="summary">
			<section class="actions">
				<div class="button">
					<p class="button-change"><a class="delete" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Delete River'); ?></span></a></p>
					<div class="clear"></div>
					<div class="dropdown container">
						<p><?php echo __('Are you sure you want to delete this River?'); ?></p>
						<ul>
							<li class="confirm"><a onclick="deleteItem(<?php echo $river->id; ?>,'river')"><?php echo __('Yep.'); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
						</ul>
					</div>
				</div>
			</section>
			<section class="meta">
				<p><a href="#"><strong><?php echo $river->get_subscriber_count(); ?></strong> <?php echo __('Subscribers'); ?></a></p>
			</section>
		</div>
	</article>

	<?php endforeach; ?> 
<?php else: ?>
	<h2 class="null"><?php echo __('No Rivers to display yet.'); ?> <em><a href="<?php echo URL::site().'river/new'; ?>"><?php echo __('Create one.');?></a></em></h2>
<?php endif; ?>
</div>