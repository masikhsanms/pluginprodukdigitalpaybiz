
<div class="groupForm">

	<div class="form-inline">

		<h5><?php echo get_post($idProduct)->post_title; ?></h5>

		<div class="form-group">

			<?php 

			foreach ($name_arr_att as $k => $name) { 

				$attribute_label_name = wc_attribute_label($k);

			?>	

				<label><?php _e('Pilih '.$attribute_label_name,'lima'); ?></label>

			<?php } ?>

			<select class="select lima-pd-nominal" style="max-width: 250px; overflow: hidden;">

				<?php echo $opt; ?>

			</select>

		</div>

		<div class="form-group">

			<label><?php _e('Masukan Nomor','lima'); ?></label>

			<input type="text" name="noMor" class="noMor" placeholder="<?php _e('Masukan Nomor','lima'); ?>">

		</div>

		<div class="form-group harga">

			<p class="title_harga"><?php _e('Harga','lima'); ?></p>

			<p class="harga_beli"><?php echo 'Rp. '.'0'; ?></p>

		</div>

		<div class="form-group">

			<button type="button" id="subCheckout"><?php _e('Beli','lima'); ?></button>

		</div>

	</div>

</div>