<?php 

$set_api = get_option('setingan_irs');

?>

<style>
	.content-setting-pd .form-group input[type="text"] {
		width: 50%;
	}
</style>

<h3><?php _e('Pengaturan Produk Digital','lima'); ?></h3>

<div class="content-setting-pd">

	<div class="col-md-12">

		<label><?php _e('Setting Tab Kategori Shortcode','lima'); ?></label>			

			<div class="form-group">

				<select multiple="multiple" class="select-pd select" name="select-tab[]">

					<?php echo getSelectCategoryPdAdmin(); ?>

				</select><br>
				<small>Shortocode : [lima-produk-digital]</small>


			</div>
			<label><?php _e('Setting API URL','lima'); ?></label>

			<div class="form-group">

				<input type="text" name="apiUrlPd" autocomplete="off" class="apiUrlPd" value="<?= $set_api['apiUrl']; ?>">
				<br>
				<em>
					 Production Server: https://paybizapi.paydia.id<br>
					 Sandbox Server: https://paybizapi.paydia.co.id 
				</em>

			</div>


			<?/*
			<label><?php _e('ID IRS'); ?></label>

			<div class="form-group">

				<input type="text" name="id_irs" autocomplete="off" class="id_irs" value="<?php $set_api['id_irs']; ?>">

			</div>

			*/?>

			<label><?php _e('Username'); ?></label>

			<div class="form-group">

				<input type="text" name="username_irs" autocomplete="off" class="username_irs" value="<?= $set_api['username_irs']; ?>">

			</div>

			

			<label><?php _e('Password'); ?></label>

			<div class="form-group">

				<input type="text" name="password_irs" autocomplete="off" class="password_irs" value="<?= $set_api['password_irs']; ?>">

			</div>


			<?/*
			<label><?php _e('PIN IRS'); ?></label>

			<div class="form-group">

				<input type="text" name="pin_irs" autocomplete="off" class="pin_irs" value="<?php $set_api['pin_irs']; ?>">

			</div>
			*/?>



			<label><?php _e('Markup PPOB/Pasca Bayar'); ?></label>

			<div class="form-group">

				<input type="text" name="markup_ppob" autocomplete="off" class="markup_ppob" value="<?= $set_api['markup_ppob']; ?>">

			</div>



			<div class="form-group">

				<button class="btnSaveSet" type="button"><?php _e('Simpan','lima'); ?></button>

			</div>

	</div>	

</div>