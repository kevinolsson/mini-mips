	<div class="row">
		<div class="large-12 columns">
			<a class="back" href="<?php echo base_url(); ?>">Back to CLI</a>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<table class="opcodes">
				<tr>
					<th>Instruction</th>
					<th>Opcode(hex)</th>
					<th>IR<small>0..5</small></th>
					<th>IR<small>6..10</small></th>
					<th>IR<small>11..15</small></th>
					<th>IR<small>16..31</small></th>
				</tr>
				<?php for($i=0;$i<$counter;$i++){?>
				<tr>
					<td><?php echo $name[$i];?></td>
					<td><?php echo $hex_opcode[$i];?></td>
					<td><?php echo $bin_opcode[$i][0]; ?></td>
					<td><?php echo substr($bin_opcode[$i][1],0, 5); ?></td>
					<td>
						<?php
						if($type[$i]=='J') {
							echo substr($bin_opcode[$i][1],4,5);
						} else {
							echo $bin_opcode[$i][2];
						}
						?>
					</td>
					<td>
						<?php
						if($type[$i]=='J') {
							echo substr($bin_opcode[$i][1],10,16);
						}
						if($type[$i]=='R') {
							echo $bin_opcode[$i][3].$bin_opcode[$i][4].$bin_opcode[$i][5];
						}
						if($type[$i]=='I') {
							echo $bin_opcode[$i][3];
						}
						?>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<table class="pipeline">
				<tr>
					<?php $index = 0; while($index<$cycle) { ?>
					<th class="cycle-<?php echo $index;?>"><a href="#<?php echo $index+1;?>"><?php echo $index+1; ?></a></th>
					<?php $index++; } ?>
				</tr>
				<?php for($i=0;$i<$counter;$i++) {	$index = 0; ?>
				<tr>
					<?php while($index<$cycle)  { ?>
					<td class="cycle-<?php echo $index;?>"><?php echo $pipeline[$i][$index];?></td>
					<?php $index++; ?>
					<?php } ?>
				</tr>					
				<?php } ?>
			</table>

			
		</div>
	</div>

	<div class="row">
		<?php for($i=0; $i<$cycle; $i++) { ?>
		<div class="large-6 columns">
			<table class="cycle">
				<tr>
					<th colspan="3" id="<?php echo $i+1;?>">Cycle <?php echo $i+1; ?></th>
				</tr>
				<!-- IF RELATED -->
				<tr>
					<td class="title">IF</td>
					<td>IF.ID.IR =</td>
					<td>
						<?php if(isset($simulate[$i][0])) { ?>
						<?php echo $simulate[$i][0]; ?>
						<?php } ?>
					</td>
				</tr> 
				<tr>
					<td class="title"></td>
					<td>IF.ID.NPC =</td>
					<td>
						<?php if(isset($simulate[$i][1])) { ?>
						<?php echo $simulate[$i][1]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>PC =</td>
					<td>
						<?php if(isset($simulate[$i][2])) { ?>
						<?php echo $simulate[$i][2]; ?>
						<?php } ?>
					</td>
				</tr>
				<!-- ID RELATED -->
				<tr>
					<td class="title">ID</td>
					<td>ID/EX.A =</td>
					<td>
						<?php if(isset($simulate[$i][3])) { ?>
						<?php echo $simulate[$i][3]; ?>
						<?php } ?>
					</td>
				</tr> 
				<tr>
					<td class="title"></td>
					<td>ID/EX.B =</td>
					<td>
						<?php if(isset($simulate[$i][4])) { ?>
						<?php echo $simulate[$i][4]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>ID/EX.IMM =</td>
					<td>
						<?php if(isset($simulate[$i][5])) { ?>
						<?php echo $simulate[$i][5]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>ID/EX.IR =</td>
					<td>
						<?php if(isset($simulate[$i][6])) { ?>
						<?php echo $simulate[$i][6]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>ID/EX.NPC =</td>
					<td>
						<?php if(isset($simulate[$i][7])) { ?>
						<?php echo $simulate[$i][7]; ?>
						<?php } ?>
					</td>
				</tr>
				<!-- EX RELATED -->
				<tr>
					<td class="title">EX</td>
					<td>EX/MEM.ALU<small>Output</small> =</td>
					<td>
						<?php if(isset($simulate[$i][8])) { ?>
						<?php echo $simulate[$i][8]; ?>
						<?php } ?>
					</td>
				</tr> 
				<tr>
					<td class="title"></td>
					<td>EX/MEM.COND =</td>
					<td>
						<?php if(isset($simulate[$i][9])) { ?>
						<?php echo $simulate[$i][9]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>EX/MEM.IR =</td>
					<td>
						<?php if(isset($simulate[$i][10])) { ?>
						<?php echo $simulate[$i][10]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>EX/MEM.B =</td>
					<td>
						<?php if(isset($simulate[$i][11])) { ?>
						<?php echo $simulate[$i][11]; ?>
						<?php } ?>
					</td>
				</tr>
				<!-- MEM RELATED -->
				<tr>
					<td class="title">MEM</td>
					<td>MEM/WB.LMD = </td>
					<td>
						<?php if(isset($simulate[$i][12])) { ?>
						<?php echo $simulate[$i][12]; ?>
						<?php } ?>
					</td>
				</tr> 
				<tr>
					<td class="title"></td>
					<td>Affected Mem =</td>
					<td>
						<?php if(isset($simulate[$i][13])) { ?>
						<?php echo $simulate[$i][13]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>MEM/WB.IR =</td>
					<td>
						<?php if(isset($simulate[$i][14])) { ?>
						<?php echo $simulate[$i][14]; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td>MEM/WB.ALU<small>OUTPUT</small> =</td>
					<td>
						<?php if(isset($simulate[$i][15])) { ?>
						<?php echo $simulate[$i][15]; ?>
						<?php } ?>
					</td>
				</tr>
				<!-- WB RELATED -->
				<tr>
					<td class="title">WB</td>
					<td>R<small>N</small> =</td>
					<td>
						<?php if(isset($simulate[$i][16])) { ?>
						<?php echo $simulate[$i][16]; ?>
						<?php } ?>
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>
	</div>
</div>

<div class="row">
	<div class="large-12 columns">
		<a class="back" href="<?php echo base_url(); ?>">Back to CLI</a>
	</div>
</div>
<br/><br/>