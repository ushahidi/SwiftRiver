<!-- Menu (Left Column) -->
		<div id="aside" class="box">

			<div class="padding box">

				<!-- Logo (Max. width = 200px) -->
				<p id="logo"><a href="#"><img src="/themes/default/media/img/logo_dashboard.png" alt="Sweeper" title="Sweeper" /></a></p>

				<!-- Search -->
				<form action="#" method="get" id="search">
					<fieldset>
						<legend><?php echo __('Search');?></legend>

						<p><input type="text" size="17" name="" class="input-text" />&nbsp;<input type="submit" value="OK" class="input-submit-02" /><br />
						<a href="javascript:toggle('search-options');" class="ico-drop"><?php echo __('Advanced Search');?></a></p>

						<!-- Advanced search -->
						<div id="search-options" style="display:none;">

							<p>
								<label><input type="checkbox" name="" checked="checked" /><?php echo __('ALL');?></label><br />
								<label><input type="checkbox" name="" /><?php echo __('Projects');?></label><br />
								<label><input type="checkbox" name="" /><?php echo __('Stories');?></label><br />
								<label><input type="checkbox" name="" /><?php echo __('Items');?></label><br />
								<label><input type="checkbox" name="" /><?php echo __('Sources');?></label>
							</p>

						</div> <!-- /search-options -->

					</fieldset>
				</form>

				<!-- Create a new project -->
				<p id="btn-create" class="box"><a href="<?php echo URL::site('/projects/edit');?>"><span><?php echo __('Create a new Project');?></span></a></p>

			</div> <!-- /padding -->

			<ul class="box">
				<li <?php if ($active == "dashboard") echo "id=\"submenu-active\"";?>><a href="<?php echo URL::site('/dashboard');?>"><?php echo __('Dashboard');?></a></li>
				<li <?php if ($active == "projects") echo "id=\"submenu-active\"";?>><a href="<?php echo URL::site('/projects');?>"><?php echo __('Projects');?></a> <!-- Active -->
					<ul>
						<?php
						// Get All Existing Projects
						foreach ($projects as $project)
						{
							$stories = $project->stories->count_all();
							?><li <?php if ($active_project_id == $project->id) echo "id=\"submenu-active-project\"";?>><a href="<?php echo URL::site('/project/')."/".$project->id; ?>"><?php echo $project->project_title;?> (<?php echo $stories; ?>)</a></li><?php
						}
						?>
					</ul>
				</li>
				<li <?php if ($active == "plugins") echo "id=\"submenu-active\"";?>><a href="<?php echo URL::site('/plugins/index');?>"><?php echo __('Plugins');?></a></li>
				<li <?php if ($active == "users") echo "id=\"submenu-active\"";?>><a href="<?php echo URL::site('/users');?>"><?php echo __('Users');?></a></li>
				<li <?php if ($active == "settings") echo "id=\"submenu-active\"";?>><a href="<?php echo URL::site('/settings');?>"><?php echo __('Settings');?></a></li>
			</ul>

		</div> <!-- /menu -->