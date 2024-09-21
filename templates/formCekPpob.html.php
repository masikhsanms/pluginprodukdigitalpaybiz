<div class="ppob-pd">
	<h5><?php echo get_post($productid)->post_title; ?></h5>
	<div class="form-group">
		<p><?php _e('Silahkan Cek Tagihan Pembayaran','lima'); ?></p>
		<label><? _e('Nomor ID Pelanggan') ?></label>
		<div class="form-group">
			<input type="text" name="nomor_pelanggan" class="nomor_pelanggan" autocomplete="off" placeholder="Masukan Nomor ID Pelanggan">
			<input type="hidden" name="idproduk" class="idproduk" value="<?php echo $productid; ?>">
		</div>
		<div class="form-group">
		<button type="button" id="chekTagihan" class="btn btn-success"><?php _e('Cek Tagihan','lima'); ?></button>
		</div>
	</div>
</div>