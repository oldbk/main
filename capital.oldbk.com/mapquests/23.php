<?php
	// ����� ���� ��� �����������
	$q_status = array(
		0 => "���������� ������� ���� ��� ����������� (%N1%/1)",
		1 => "�������� ������� ���� (%N1%/10), ����� (%N2%/1), ���� ��������� �������� (%N3%/1)",
	);
	
	if (!isset($questexist) || $questexist === FALSE || $questexist['q_id'] != 23) return;

	$step = $questexist['step'];

	$sf = basename(basename($_SERVER['PHP_SELF']),".php");
	
	$ai = explode("/",$questexist['addinfo']);

	if ($sf == "mlvillage") {
		if ($step == 0 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001) {
					$mldiag = array(
						0 => "�� ���� �� ��� ������ ������ ���� ��������? ��������-�� �� ��������, ��������, ��� � ������ � ������� � �������. � �������� ��� �����, ��� � ���� ����, ���� ������ ������ � ������� �������� ����������, ��� �� ������ ������� �������",
						2003 => "�� ���� ��� ���� �������� ���� ���-������ �������� � �����. ���������� ��� �� ������",
					);
				} elseif ($_GET['qaction'] == 2003) {
					$mldiag = array(
						0 => "����-����, �� ��������. ��� ���, ����������, ����������� ����. ������ ������ ������ ��������",
						2004 => "���, ���-�� �������� � ������ ������ ����. �� ��� � �������",
					);
				} elseif ($_GET['qaction'] == 2004) {
					$mldiag = array(
						0 => "����-���� ���������. ����������� �� ����� ����, � �� �����. ������� � ����, �� ���������� �� �������� ����������� � �����. ����� ���� �������� ���-������ ������",
						2005 => "��� ���? �� ������, ��� ��� ���? ����� ���� ���-�� ���? ���? ��, ����� �� �������. ����!",
					);
				} elseif ($_GET['qaction'] == 2005) {
					mysql_query('START TRANSACTION') or QuestDie();
					SetQuestStep($user,23,1) or QuestDie();					
					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "������, ��� ���� ���� ������?",
					2001 => "� ����������� �������� � ������ ������. ������� ����, �� ��������� �������� ���������� ������� ���� � ����, �-�� � ����� ������ �� ������� �� ���� �� ����� ����� ���������?",
					11111 => "�����, ������ �������������",
				);
			}
			
		}

		if ($step == 1 && ((isset($_GET['quest']) && $_GET['quest'] == 3) || (isset($_GET['qaction']) && $_GET['qaction'] > 2000 && $_GET['qaction'] < 3000))) {
			$mlqfound = false;
			$qi1 = QItemExistsCountID($user,3003005,10);
			$qi2 = QItemExistsID($user,3003082);
			$qi3 = QItemExistsID($user,3003083);
	
			if ($qi1 !== FALSE && $qi2 !== FALSE && $qi3 !== FALSE) {
				$mlqfound = true;
				$todel = array_merge($qi1,$qi2,$qi3);
			}


			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 2001 && $mlqfound) {
					$mldiag = array(
						0 => "������ � ���� ��� ���������! ������ ������� ����� ��� ����� ������.",
						2003 => "�������, ���� ���������� �����������. �� ���� �� �����, ��� �� �� ��� ������� ��� ������� ������� � �������",
					);
				} elseif ($_GET['qaction'] == 2003 && $mlqfound) {
					$mldiag = array(
						0 => "� ����� ������ � ���� ����� ����� ����� �� �������. ���, ��� ������, ������ ������� ���� ���������.",
						2004 => "�������� �������. ����!",
					);
				} elseif ($_GET['qaction'] == 2004 && $mlqfound) {
					mysql_query('START TRANSACTION') or QuestDie();

					PutQItem($user,3003084,"������",0,$todel) or QuestDie();
					addchp ('<font color=red>��������!</font> ������ ������� ��� <b>������� ����</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					mysql_query('COMMIT') or QuestDie();
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "������, �� ������ ��� ��, ��� � ������?",
					2001 => "��! ��� ����, ���� ������ �� ���� � ��������� ����� � �������� �� ������ ������� �����! �������, ����� ������?",
					11111 => "��� ���, ������� �������",
				);
				if (!$mlqfound) unset($mldiag[2001]);
			}
		}

		if (((isset($_GET['quest']) && $_GET['quest'] == 1) || (isset($_GET['qaction']) && is_numeric($_GET['qaction']) && $_GET['qaction'] < 1000))) {
			$mlqfound = false;
			$todel = QItemExistsID($user,3003084);

			if ($todel !== FALSE) $mlqfound = true;

			if (isset($_GET['qaction'])) {
				if ($_GET['qaction'] == 1 && $mlqfound) {
					$mldiag = array(
						0 => "�������, ������ �������! ���, �����, �� ������������� �������� ������ �������.",
						3 => "��������� ���. ����!",
					);
				} elseif ($_GET['qaction'] == 2) {
					mysql_query('START TRANSACTION') or QuestDie();
					unsetQuest($user) or QuestDie();
					mysql_query('COMMIT') or QuestDie();
					unsetQA();		
				} elseif ($_GET['qaction'] == 3 && $mlqfound) {
					// ����� �������
					mysql_query('START TRANSACTION') or QuestDie();				

					$r = AddQuestRep($user,150) or QuestDie();
					$m = AddQuestM($user,1,"����������") or QuestDie();
					$e = AddQuestExp($user) or QuestDie();
	
					PutQItem($user,15565,"����������",0,$todel,255,"eshop") or QuestDie();
					PutQItem($user,105,"����������",7,array(),255,"shop",3) or QuestDie();
	
					$msg = "<font color=red>��������!</font> �� �������� <b>������� ������ ���� ������</b> � <b>������ �������</b>, <b>".$r."</b> ���������, <b>".$e."</b> ����� � <b>".$m."</b> ��. �� ���������� ������!";
					addchp ($msg,'{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

					UnsetQuest($user) or QuestDie();	
					mysql_query('COMMIT') or QuestDie();			
					unsetQA();
				} else {
					unsetQA();
				}
			} else {
				$mldiag = array(
					0 => "������, �� ������ ��� ��, ��� � ������?",
					1 => "��� ��������������. ������������ ���� ������ ������, �� ������ �����, ��� ������ ����� ������� � ���� �����. ��������� ����������� ������������ ����� ������� � ������ �������� ������ ������� ��� ������ ����� �������������� �� ����������� ������?",
					2 => "���, � ���� ���������� �� ������� (� ����, ��� ����� ����� ��������� ������ ����� 20 �����)",
					11111 => "���� ��� ���",
				);
				if (!$mlqfound) unset($mldiag[1]);
			}	

		}
	}

	if ($sf == "mlfort" && $step == 1 && !QItemExists($user,3003082)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "���?! ����������, �����?! � ���������� ����� ���� ���������� ����� ��� �������",
					3 => "�, ������ ���, � ���� ��� ������� �������������. ���� �� �� ����� �������� ������� ������� ��������� �����, �� �������� � �� �� ����� ������ ������� ��� �����������.",
				);
			} elseif ($_GET['qaction'] == 3) {
				$mldiag = array(
					0 => "�� ������, ��� ����� ����� ����������?!",
					4 => "���������� ������, � ����� ������ ����������� ���� �� ����� �����",
				);
			} elseif ($_GET['qaction'] == 4) {
				$mldiag = array(
					0 => "������, ������ ����� � �������� ��� �����������.",
					5 => "�������! ������ ������� � �������� ���������� � ����� �� �������! ����!",
				);
			} elseif ($_GET['qaction'] == 5) {
				mysql_query('START TRANSACTION') or QuestDie();				

				PutQItem($user,3003082,"��������") or QuestDie();
				addchp ('<font color=red>��������!</font> �������� ������� ��� <b>�������� �������� ����������</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "������� ������, �� ������������. ��� ���������� �����, � �� �������. ��� ��� �������� � �� ����. �� ��� ����� ��� �����? �������, ������!",
				1 => "������� ������� ����� ������! ������ ����, ��� ���������� ��������� � ������, � ��������� ����������� ���� ������ �� ������� ���� �� ������ ���� ����� �����������!",
				11111 => "�� ������-�� � ���������",
			);
		}	
	}

	if ($sf == "mlmage" && $step == 1 && !QItemExists($user,3003083)) {
		if (isset($_GET['qaction'])) {
			if ($_GET['qaction'] == 1) {
				$mldiag = array(
					0 => "� ���� �� ������ ������� ���� �� �������?",
					3 => "������� �������� ��������� ���� ������ ���������� � ������� ���������� ����� ��������������� ���������� ������ ���������",
				);
			} elseif ($_GET['qaction'] == 3) {
				$mldiag = array(
					0 => "��� ��� �������, �������. ��� ���� �� ��� ����� �� �������� �������?",
					4 => "���� � ���, ��� ������ ���������� �������� ������� �� ������. �� ���� �� ��� ����� ����� ������� ������� ��� �� ����������, �� ������ ������� ������� ���� �������� �� ���� � �������� �����������!",
				);
			} elseif ($_GET['qaction'] == 4) {
				$mldiag = array(
					0 => "������?! � ���� � �������, � ������������ �� �� ��� ����!",
					5 => "�� ��� ������ �� ����������� ������ ������� ������ � ������ �������������� �� ����������� ���������",
				);
			} elseif ($_GET['qaction'] == 5) {
				$mldiag = array(
					0 => "��� ������! ������-�� ��������� �� ������ � ������ ���� ��������, �� ��� �� ������� ����� ���� ������ ���������, � ������� ������ ����������. �� ������ ���� �� ������������, ��� � ��� ����� ������ ���� � ��������� ���",
					6 => "��������� ����������, ���� ��������������. ����!",
				);
			} elseif ($_GET['qaction'] == 6) {
				mysql_query('START TRANSACTION') or QuestDie();				

				PutQItem($user,3003083,"���") or QuestDie();
				addchp ('<font color=red>��������!</font> ��� ������� ��� <b>���� ��������� ��������</b>','{[]}'.$user['login'].'{[]}',-1,$user['id_city']) or QuestDie();

				mysql_query('COMMIT') or QuestDie();			
				unsetQA();
			} else {
				unsetQA();
			}
		} else {
			$mldiag = array(
				0 => "�� ����� ���� ����� ���������. ����� ������ � �� ������ ����? ������.",
				1 => "����������� ����, � ���������. � ������ �� ������� ������� �������",
				33333 => "�������, �� ������ ������� ���������� ����� �� ��������� �������. ���� ��������� ���� � ����� ������.",
				44444 => "� ������ ����� �����, ��� �� ������ ������� ���������� ���� �������. ���� ��� ������, �� ��� �� �� �������� ���������.",
				11111 => "������ ������, ������ ������",
			);
		}	
	}

?>