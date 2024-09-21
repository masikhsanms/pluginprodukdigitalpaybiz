<?php 

    $set_irs = get_option('setingan_irs');

	$markup_harga = $set_irs['markup_ppob'];

 ?>

<div class="infoTagihan" style="padding: 0px 30px 30px 30px;">

	<h3><?php _e('Informasi Tagihan'); ?></h3>

	<table class="table table-striped" width="100%">

		<tr>

			<th ><?= 'ID Pelanggan'; ?></th>

			<td class="idpelanggan"><?= $idpelanggan;  ?></td>

		</tr>

		<tr>

			<th><?= 'Produk'; ?></th>

			<td><?= $produk;  ?></td>

		</tr>

		<tr>

			<th><?= 'Nama'; ?></th>

			<td><?= $nama;  ?></td>

		</tr>

		<tr>

			<th><?= 'Total Tagihan + Admin'; ?></th>

			<td class="tagihan">
				<?php
					if( !empty($tagihan) || $tagihan != 0 ){
						echo wc_price($tagihan+$markup_harga);  
					}else{
						echo 'Sudah Dibayar';
					}
				?>
			</td>

		</tr>

	</table>  

	

	<div class="form-group" style="margin-top:20px;">

		<input type="hidden" name="_produk" id="_produk" value="<?= $idProduct; ?>">

		<input type="hidden" name="_harga" id="_harga" value="<?= $tagihan+$markup_harga; ?>">
		
		<?php if( !empty($tagihan) || $tagihan != 0 ){ ?>

		<button class="btn btn-info bayarTagihan" type="button"><?php _e('Bayar'); ?></button>
		
		<?php } ?>
	</div>

</div>