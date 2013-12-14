<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Simulate extends CI_Controller {

	var $simulate;

	/**
	 *  COMPARC Machine Project - miniMIPS 
	 *	Data Hazard: No Forwarding
	 *	Control Hazard: Pipeline Flush
	 *	List of instructions and corresponding opcodes and functions:
	 *		DADDU	000000	101101
	 *		DSUBU	000000	101111
	 *		AND 	000000	100100
	 *		OR 		000000	100101
	 *		SLT 	000000	101010
	 *		BEQZ	000100	N/A
	 *		LD 		101111 	N/A
	 *		SD 		111111	N/A
	 *		DADDIU	011001	N/A
	 *		J 		000010 	N/A
	 */
	public function index()
	{
		$this->load->helper('url');
		//$this->load->vars($global);

		if(!isset($_POST['instructions'])) {
			
			redirect(base_url());

		}

		$counter = 0;
		$address = 0;

		$branch = true;// this is true, since all memory starts at 0

		// Trim last ; from instructions
		$instructions = trim($_POST['instructions']);
		$instructions = substr_replace($instructions, "", -1);

		// STEP 1 -- Generate the OPCODES per functions
		$instructions = explode(";",$instructions);
		foreach($instructions as $instruction) {
			// Pass to current so we have a clean variable to use
			$current = $instruction;
			$name[$counter] = $instruction;

			// Split instruction and paramters and strip whitesapces
			$instruction = explode(" ",$instruction,2);
			$instruction[0]=preg_replace('/\s+/', '', $instruction[0]);

			// Check if first portion is a label (ie 'L1')
			if (strpos($instruction[0],':') !== false) {

				$instruction[0] = substr($instruction[0],1);
				$instruction[0] = substr($instruction[0], 0, -1);

				// Check if label belongs to a BEQZ flag
				if(isset($BEQZ_FLAG[0])) {
					if($instruction[0]==$BEQZ_FLAG[0]) {
						// ((current address - beqz address ) - 4 ) / 4
						// It is? Cool, do this weird formula I came up with
						$x = $address - $BEQZ_FLAG[2];
						$x = $x - 4;
						if($x!=0) {
							$x = $x/4;
						}
						// Assign the opcode to the beqz istruction. Its been waiting.
						$opcode[$BEQZ_FLAG[1]][3] = $x;
					}
				}

				if(isset($J_FLAG[0])) {
					if($instruction[0]==$J_FLAG[0]) {
						$x = $address - $J_FLAG[2];
						$x = $x - 4;
						if($x!=0) {
							$x = $x/4;
						}
						$opcode[$J_FLAG[1]][1] = $x;
					}
				}


    			// Adjust all instruction arrays
    			$label = $instruction[0];
    			$instruction = $instruction[1];

    			//Re-split again    			
    			$instruction = explode(" ",$instruction,2);
				$instruction[0]=preg_replace('/\s+/', '', $instruction[0]);

			}

			// Generate OPCODE(6) 
			// All instructions that didn't specify an opcode will default to 0
			// All instructions that didn't specify a function will default to null
			$opcode[$counter][0] = 0;
			$function[$counter] = null;
			switch($instruction[0]) {
				case 'DADDU':
					$function[$counter] = 45;
					$type[$counter] = 'R';
					break;
				case 'DSUBU':
					$function[$counter] = 47;
					$type[$counter] = 'R';
					break;
				case 'AND':
					$function[$counter] = 36;
					$type[$counter] = 'R';
					break;
				case 'OR':
					$function[$counter] = 37;
					$type[$counter] = 'R';
					break;
				case 'SLT':
					$function[$counter] = 42;
					$type[$counter] = 'R';
					break;
				case 'BEQZ':
					$opcode[$counter][0] = 4;
					$type[$counter] = 'I';
					break;
				case 'LD':
					$opcode[$counter][0] = 47;
					$type[$counter] = 'I';
					break;
				case 'SD':
					$opcode[$counter][0] = 63;
					$type[$counter] = 'I';
					break;
				case 'DADDIU':
					$opcode[$counter][0] = 25;
					$type[$counter] = 'I';
					break;
				case 'J':
					$opcode[$counter][0] = 2;
					$type[$counter] = 'J';
					break;


			}

			// Generate parameters
			if(isset($instruction[1])){
				$counter2 = 0;

				// Split parameters into individual entities 
				$parameters = explode(",",$instruction[1]);

				foreach($parameters as $parameter) {
					//echo $parameter;

					//Strip whitespace
					$parameter = preg_replace('/\s+/', '', $parameter);

					// If instruction is R-type
					if($type[$counter]=='R') {						
						// Input => Instruction RD,RS,RT
						// Generate to => RS(5) RT(5) RD(5) (5) Func(6)
						
						// Trim R from the variable
						$parameter = substr($parameter, 1);

						switch($counter2) {
							case 0:
								// First loop, RD
								$opcode[$counter][3] = $parameter;
								//echo $opcode[3].' '.$counter2.'<br/>'; 
								break;
							case 1:
								// Second loop, RS
								$opcode[$counter][1] = $parameter;
								//echo $opcode[1].' '.$counter2.'<br/>'; 
								break;
							case 2:
								// Last loop, RT
								$opcode[$counter][2] = $parameter;
								//echo $opcode[2].' '.$counter2.'<br/>'; 
								break;
						}	
					} // End of R type conditional
					if($type[$counter]=='I') {
						// Input => Instruction RT, Offset(RS)
						// Generate to => RS(5), RT(5), Immediate (16)
						

						switch($counter2) {
							case 0:
								// Trim R from fariable
								$parameter = substr($parameter, 1);
								// First loop, RT (or RD for DADDIU)
								if ($instruction[0]=='BEQZ') {
									$opcode[$counter][1] = $parameter;
									$opcode[$counter][2] = 0;
									
								} else {
									$opcode[$counter][2] = $parameter;
									
								}

								break;
								
							case 1:
								
								// Second loop, possible scenarios:
								//	- BEQZ R5, L1
								//	- SW R3, 1008(R0)
								//	- DADDIU R2,R3, 1000
								if ($instruction[0]=='BEQZ') {
									$opcode[$counter][3] = 0; //temporary
									$BEQZ_FLAG[0] = substr($parameter, 1); //Should return the L register
									$BEQZ_FLAG[1]  = $counter;
									$BEQZ_FLAG[2]  = $address;
								}
								if($instruction[0] =='SD' ||$instruction[0] =='LD') {
									if (strpos($parameter,'(') !== false) {
		 								$opcode[$counter][1] = substr($parameter, 4);
										$opcode[$counter][1] = strtr($opcode[$counter][1], array('(' => '', ')' => ''));
										$opcode[$counter][1] = substr($opcode[$counter][1], 1);
										

										// Grab immediate
										$opcode[$counter][3] = substr($parameter, 0 ,4);
										 								

									}
								}
								if($instruction[0] == 'DADDIU') {
									$opcode[$counter][1] = substr($parameter, 1);
								}
								break;

							case 2:
								//immediate of DADDIU
								$opcode[$counter][3] = $parameter;
								
						}
					}
					if($type[$counter]=='J') {
						// J Types
						// Input => J L1
						// Output => Offset(26) - Address of where Label is located / 4'
						//$opcode[$counter][1] = 0;
						$J_FLAG[0] = substr($parameter, 1);
						$J_FLAG[1] = $counter;
						$J_FLAG[2] = $address;
					}
					$counter2++;
				}
			} // End of parameter generation

			// $opcode[$counter][3] will look something like 1000 right now.
			// We have to split that into 4 digits and convert each into binary.
			if($instruction[0]=='DADDIU'||$instruction[0] =='SD'||$instruction[0] =='LD') {
				$opcode[$counter][3] = str_split($opcode[$counter][3]);
			}

		$counter++;			
		$address += 4;
		}

		// BUILD THE BINARY OF THE OPCODES

		for($i=0;$i<$counter;$i++) {
			// Convert the OPCODES into binary
			//echo $name[$i].'<br/>';

			// - OPCODE[0] (6) to binary first;
			$bin_opcode[$i][0] = (decbin($opcode[$i][0]));
			$bin_opcode[$i][0] = substr("000000",0,6-strlen($bin_opcode[$i][0])).$bin_opcode[$i][0];
			//echo $bin_opcode[$i][0];

			// - OPCODE[1] to binary -------------
				// IF J TYPE
				if($type[$i]=='J') {
					$bin_opcode[$i][1] = (decbin($opcode[$i][1]));
					$bin_opcode[$i][1] = substr("00000000000000000000000000",0,26-strlen($bin_opcode[$i][1])).$bin_opcode[$i][1];
					//echo $bin_opcode[$i][1];
				} else {
				// IF R or I type
					$bin_opcode[$i][1] = (decbin($opcode[$i][1]));
					$bin_opcode[$i][1] = substr("00000",0,5-strlen($bin_opcode[$i][1])).$bin_opcode[$i][1];
					//echo $bin_opcode[$i][1];
				}
			// -----------------------------------
			// OPCODE[2] to BINARY
				// IF R or I type
				if($type[$i]=='R'||$type[$i]=='I') {
					$bin_opcode[$i][2] = (decbin($opcode[$i][2]));
					$bin_opcode[$i][2] = substr("00000",0,5-strlen($bin_opcode[$i][2])).$bin_opcode[$i][2];
					//echo $bin_opcode[$i][2];
				}
			// -----------------------------------
			// OPCODE[3] to BINARY
				if($type[$i]=='R') {
					$bin_opcode[$i][3] = (decbin($opcode[$i][3]));
					$bin_opcode[$i][3] = substr("00000",0,5-strlen($bin_opcode[$i][3])).$bin_opcode[$i][3];
					//echo $bin_opcode[$i][3];
				}
				if($type[$i]=='I'){
					if($bin_opcode[$i][0]=='000100') {
						//BEQZ
						$bin_opcode[$i][3] = (decbin($opcode[$i][3]));
						$bin_opcode[$i][3] = substr("0000000000000000",0,16-strlen($bin_opcode[$i][3])).$bin_opcode[$i][3];
						//echo $bin_opcode[$i][3];
					} else {
						//print_r($opcode[$i][3]);

						$bin_opcode[$i][3][0] = (decbin($opcode[$i][3][0]));
						$bin_opcode[$i][3][0] = substr("0000",0,4-strlen($bin_opcode[$i][3][0])).$bin_opcode[$i][3][0];

						$bin_opcode[$i][3][1] = (decbin($opcode[$i][3][1]));
						$bin_opcode[$i][3][1] = substr("0000",0,4-strlen($bin_opcode[$i][3][1])).$bin_opcode[$i][3][1];

						$bin_opcode[$i][3][2] = (decbin($opcode[$i][3][2]));
						$bin_opcode[$i][3][2] = substr("0000",0,4-strlen($bin_opcode[$i][3][2])).$bin_opcode[$i][3][2];

						$bin_opcode[$i][3][3] = (decbin($opcode[$i][3][3]));
						$bin_opcode[$i][3][3] = substr("0000",0,4-strlen($bin_opcode[$i][3][3])).$bin_opcode[$i][3][3];

						$bin_opcode[$i][3] = $bin_opcode[$i][3][0].$bin_opcode[$i][3][1].$bin_opcode[$i][3][2].$bin_opcode[$i][3][3];
						//echo $bin_opcode[$i][3];
					}
				}
			// FINISHING UP ON R TYPES
				if($type[$i]=='R') {
					$bin_opcode[$i][4] = '00000';
					//echo $bin_opcode[$i][4];

					$bin_opcode[$i][5] = (decbin($function[$i]));
					$bin_opcode[$i][5] = substr("000000",0,6-strlen($bin_opcode[$i][5])).$bin_opcode[$i][5];
					//echo $bin_opcode[$i][5];
				}
			// -----------------------------------
		}

		// CONVERTING BINARY TO HEX OPCODES
		for($i=0;$i<$counter;$i++) {
			//echo $name[$i].'<br/>';

			if($type[$i]=='R'){
				// Append from [0] to [5]
				$hex_opcode[$i] = $bin_opcode[$i][0].$bin_opcode[$i][1].$bin_opcode[$i][2].$bin_opcode[$i][3].$bin_opcode[$i][4].$bin_opcode[$i][5];
			}
			if($type[$i]=='I') {
				// Append from [0] to [3]
				$hex_opcode[$i] = $bin_opcode[$i][0].$bin_opcode[$i][1].$bin_opcode[$i][2].$bin_opcode[$i][3];				
			}

			if($type[$i]=='J') {
				$hex_opcode[$i] = $bin_opcode[$i][0].$bin_opcode[$i][1];		
			}

			// Split into 4, then rebuild into HEX
			$hex_opcode[$i] = str_split($hex_opcode[$i], 4);
			for($k=0;$k<8;$k++) {
				$hex_opcode[$i][$k] = dechex(bindec($hex_opcode[$i][$k]));
				//echo $hex_opcode[$i][$k];
			}

			$hex_opcode[$i] = implode("", $hex_opcode[$i]);

		}


		// ACTUA EXECUTION WHILE DRAWING THE MAP
		$cycle=0;
		$FIN = false;
		$signal = false;
		while(!isset($complete[$counter-1])) { // While last instruction not complete
			for($i=0;$i<$counter;$i++) {
				//echo $i.' '.$cycle.'<br/>';
				if(isset($pipeline[$i][$cycle-1])) { // If block to the left is set
					if(($branch['flag']==TRUE&&$i==$branch['i'])||$branch['flag']==FALSE) {
						switch($pipeline[$i][$cycle-1]) {							
							// Insert next instruction
							// CALL EXECUTION HERE LATER ON!;
							case 'IF':
								$pipeline[$i][$cycle] = 'ID';
								// Simulate ID
								$this->simulate_id(
									$cycle,
									$hex_opcode,
									$bin_opcode,
									$type[$i],
									$i
								);

								break;
							case 'ID':
								$pipeline[$i][$cycle] = 'EX';
								'EX '.$i.' '.$cycle.'<br/>';
								break;
							case 'EX':
								$pipeline[$i][$cycle] = 'MEM';
								//echo 'MEM '.$i.' '.$cycle.'<br/>';
								break;
							case 'MEM':
								$pipeline[$i][$cycle] = 'WB';
								//echo 'WB'.$i.' '.$cycle.'<br/>';
								// issue complete flag
								$complete[$i] = TRUE;
								//echo $name[$i].' is complete! - '.$i.' '.$cycle.'<br/>';
								break;
							case 'WB':
								break;
						}
					}
				} else {
					// block to the left does not exist
					if(isset($pipeline[$i-1][$cycle])) {
						if($branch['flag']==TRUE){
						// If BRANCH is true
							switch($pipeline[$i-1][$cycle]) {
								// Insert next instruction
								// CALL EXECUTION HERE LATER ON!;
								case 'IF':
									// do nothing (FIN?)
									$FIN = TRUE;
									break;
								case 'ID':
									$pipeline[$i][$cycle] = 'IF';
									// Simulate IF
									$this->simulate_if(
										$cycle,
										$hex_opcode,
										$bin_opcode,
										$i
									);
									break;
								case 'EX':
									break;
								case 'MEM':
									// do nothing
									break;
								case 'WB':

									if($branch['jump']==TRUE) {
										// JUMP TO NEW $i
										// TURN OFF BRANCH 
										

										// determine what the new $i is based of $branch['new']
										$new = ($i - $branch['i'])+$branch['new'];
										while($i!=$new) {
											$complete[$i] = TRUE;
											$i++;
										}
										$i = $new;
										$pipeline[$i][$cycle] = 'IF';
										// Simulate IF
										$this->simulate_if(
											$cycle,
											$hex_opcode,
											$bin_opcode,
											$i
									);	

									} else {
										// If we are not going to jump
										$pipeline[$i][$cycle] = 'IF';
										// Simulate IF
										$this->simulate_if(
											$cycle,
											$hex_opcode,
											$bin_opcode,
											$i
										);
									}

									// TURN OFF BRANCH
									$signal = true;
									break;
							}
						} else {
						// If BRANCH is false
						$FIN = TRUE;
						}
					} else { // pipeline on top does not exist
						if($branch['flag']==TRUE) {
							if(isset($complete[$i])||$FIN==TRUE){
								// DO NOTHING
							} else {
								$pipeline[$i][$cycle] = 'IF';
								if($branch['flag']==FALSE) {
									// check for branch
									$branch = $this->ifbranch($opcode,$bin_opcode,$i);
								}
									// Simulate IF
									$this->simulate_if(
										$cycle,
										$hex_opcode,
										$bin_opcode,
										$i
									);
							}
						} else {
							if($FIN==TRUE) {
								// do nothing
							} else {
								if($cycle==0&&$i==0){
									$pipeline[$i][$cycle] = 'IF';
									if($branch['flag']==FALSE) {
										// check for branch
										$branch = $this->ifbranch($opcode,$bin_opcode,$i);
										// Simulate, providing the function with the cycle count
										// The binary data and the current instruction
									}
									// Simulate IF
									$this->simulate_if(
										$cycle,
										$hex_opcode,
										$bin_opcode,
										$i
									);
								}
								if(isset($complete[$i-1])&&!isset($complete[$i])) {
									$pipeline[$i][$cycle] = 'IF';
									if($branch['flag']==FALSE) {
										// check for branch
										$branch = $this->ifbranch($opcode,$bin_opcode,$i);
									}
									// Simulate IF
									$this->simulate_if(
										$cycle,
										$hex_opcode,
										$bin_opcode,
										$i
									);
								}
							}

						}

					}
				}

			}
			if($signal==TRUE) {
				$branch['flag'] = false;
				$branch['jump'] = false;
				$branch['new'] = null;
				$branch['i'] = null;
			}
			$FIN = false;
			$cycle++;
		}

		//print_r($simulate);

		for($i=0;$i<$counter;$i++){
			$index = 0;
			while($index<$cycle) {
				if(!isset($pipeline[$i][$index])) {
					$pipeline[$i][$index] = ' . ';
				}
				$index++;
			}
		}



		// Push data to view
		$data['bin_opcode'] = $bin_opcode;
		$data['hex_opcode'] = $hex_opcode;
		$data['counter'] = $counter;
		$data['cycle'] = $cycle;
		$data['name'] = $name;
		$data['type'] = $type;
		$data['pipeline'] = $pipeline;
		$data['simulate'] = $this->simulate;


		$this->load->view('shared/header');
		$this->load->view('simulate',$data);
		$this->load->view('shared/footer');

	}

	public function ifbranch($opcode, $bin_opcode, $i) {
		$opcode[$i][0] = decbin($opcode[$i][0]);
		if($opcode[$i][0]=='000100'||$opcode[$i][0]=='000010') {
		// If 'IF' is a BEQZ or a J
			$branch['flag'] = TRUE;
			$branch['jump'] = FALSE; // TRUE unless stated otherwise in cycles
			$branch['i'] = $i;
			if($opcode[$i][0]=='000100') {
				//BEQZ
				// GRAB ADDRESS
				$branch['new'] = bindec($bin_opcode[$i][3]); // determines how far it will jump
			} else if($opcode[$i][0]=='000010') {
				//JUMP
				$branch['jump'] = TRUE; // J ALWAYS JUMPS
				// GRAB ADDRESS
				$branch['new'] = bindec(substr($bin_opcode[$i][1],22,4));
			}
			//$branch['new'] += 1; //increment by 1
		} else {
			$branch['flag'] = false;	
		}
		return $branch;
	}

	public function simulate_if($cycle,$hex_opcode,$bin_opcode,$i) {
		//$this->load->vars($global);
		// Asign to the specific cycle, possible data related to the specific instruction
		// IF SIMULATES THE FOLLOWING
		// [0] - IF/ID.IR = the instruction register
		// [1] - IF/ID.NPC
		// [2] - PC

		// IF/ID.IR = hex opcode of the instruction
		$this->simulate[$cycle][0] = $hex_opcode[$i];

		// IF/ID.NPC&PC- The next address (current address + 4);
		// So if current address is 0000 .. 0000 => 0000 .. 0004
		$this->simulate[$cycle][1] = ($i*4)+4;
		// convert to hex
		$this->simulate[$cycle][1] = dechex($this->simulate[$cycle][1]);
		// add 0 paddings
		$this->simulate[$cycle][1] = substr("0000000000000000",0,16-strlen($this->simulate[$cycle][1])).$this->simulate[$cycle][1];

		$this->simulate[$cycle][2] = $this->simulate[$cycle][1];
	}

	public function simulate_id($cycle,$hex_opcode,$bin_opcode,$type,$i){
		//$this->load->vars($global);
		// [3] - ID/EX.A => These 2 are a little bit tricky, has to be executed
		// [4] - ID/EX.B    per instruction type to make sure its correct
		// [5] - ID/EX.IMM => last 4 digits of HEX opcode with 0 padding
		// [6] - ID/EX.IR => copy over from IF
		// [7] - ID.EX.NPC => copy over from IF
		$this->simulate[$cycle][3] = substr($bin_opcode[$i][1],0, 5);
		$this->simulate[$cycle][3] = substr("0000000000000000",0,16-strlen($this->simulate[$cycle][3])).$this->simulate[$cycle][3];
		if($type=='J') {
			$this->simulate[$cycle][4] = substr($bin_opcode[$i][1],4,5);
		} else {
			$this->simulate[$cycle][4] = $bin_opcode[$i][2];
		}
		$this->simulate[$cycle][4] = substr("0000000000000000",0,16-strlen($this->simulate[$cycle][4])).$this->simulate[$cycle][4];


		$this->simulate[$cycle][5] = substr($hex_opcode[$i],-4);
		$this->simulate[$cycle][5] = substr("0000000000000000",0,16-strlen($this->simulate[$cycle][5])).$this->simulate[$cycle][5];

		$this->simulate[$cycle][6] = $hex_opcode[$i];

		$this->simulate[$cycle][7] = ($i*4)+4;
		$this->simulate[$cycle][7] = dechex($this->simulate[$cycle][7]);
		$this->simulate[$cycle][7] = substr("0000000000000000",0,16-strlen($this->simulate[$cycle][7])).$this->simulate[$cycle][7];


	}
}
