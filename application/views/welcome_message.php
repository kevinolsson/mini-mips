	<div class="row">
		<!--<div class="large-12 columns">
			<h1>miniMIPS</h1>
			<h5>A simplified MIPS64 processor simulator</h5>
		</div>-->
		
		<?php if(isset($error)) { ?>
		<div class="large-12 columns">
			<p class="error">
					<?php echo $error; ?>				
			</p>
		</div>
		<?php } ?>

		<div class="large-12 columns">
			<?php
			 	echo form_open('simulate/');
				echo form_textarea(
					array('
						name'=>'instructions',
						'id'=>'command-line',
						'spellcheck' => 'false',
						'value' =>
						'BEQZ R5, L1;&#13;&#10;SD R3, 1008(R0);&#13;&#10;DADDIU R2,R3, 1000;&#13;&#10;OR R5,R2,R6;&#13;&#10;AND R7,R1,R3;&#13;&#10;L1: SD R2, 1005(R1);&#13;&#10;DADDU R2,R1,R1;'
						)
					);
			 	echo form_submit(array('class'=>'simulate','value'=>'Simulate!')); 
				echo form_close();
			?>

		</div>
	</div>