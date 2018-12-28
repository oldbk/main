<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>

	<style>
		.pers {
			float: left;
			margin-right: 20px;
		}
	</style>
</head>
<body>
<?php
die();

$dalls = array();
$links = array(
	'crit' => 'krits',
	'tank' => 'tanks',
	'vert' => 'wilds',
);
for ($level = 8; $level < 15; $level++) {
    $path = 'static';
    if($level > 10) {
        $path = 'anim';
    }
	foreach (array('crit', 'tank', 'vert') as $complClass) {
		$compl1 = array();
		$compl2 = array();
		foreach (['armour','boots','hands','helm','mace','neck','ring','shield','sword','tink'] as $item) {
			$compl1[$item] = sprintf('assets/%s/%d/%s/1/%s.gif', $links[$complClass], $level, $path, $item);
			$compl2[$item] = sprintf('assets/%s/%d/%s/2/%s.gif', $links[$complClass], $level, $path, $item);
        }

		$dalls[$complClass][$level][1] = $compl1;
		$dalls[$complClass][$level][2] = $compl2;
	}
}
?>

<?php foreach ($dalls as $complClass => $dallList): ?>
	<?php foreach ($dallList as $level => $items): ?>
		<?php foreach ($items as $item): ?>
			<table width="196" class="pers" cellspacing="0" cellpadding="0">
				<tbody>
				<tr>
					<td colspan="3">
						<?= sprintf('%s - %s', $complClass, $level) ?>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tbody>
							<tr>
								<td>
									<img src="<?= $item['tink'] ?>" width="60" height="20">
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?= $item['neck'] ?>" width="60" height="20">
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?= $item['sword'] ?>" width="60" height="60">
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?= $item['armour'] ?>" width="60" height="80">
								</td>
							</tr>
							<tr>
								<td>

								</td>
							</tr>
							<tr>
								<td>
									<table cellspacing="0" cellpadding="0">
										<tbody>
										<tr>
											<td>
												<img src="<?= $item['ring'] ?>" width="20" height="20">
											</td>
											<td>
												<img src="<?= $item['ring'] ?>" width="20" height="20">
											</td>
											<td>
												<img src="<?= $item['ring'] ?>" width="20" height="20">
											</td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>
							</tbody>
						</table>
					</td>
					<td valign="top">
						<img src="http://i.oldbk.com/i/shadow/0.gif" width="76" height="209" alt="Красная Плесень">
					</td>
					<td width="62" valign="top">

						<table width="100%" cellspacing="0" cellpadding="0">
							<tbody>
							<tr>
								<td>
									<img src="<?= $item['helm'] ?>" width="60" height="60">
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?= $item['hands'] ?>" width="60" height="40">
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?= $item['shield'] ?>" width="60" height="60">
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?= $item['boots'] ?>" width="60" height="40">
								</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		<?php endforeach; ?>
	<?php endforeach; ?>
	<div class="" style="clear: both"></div>
<?php endforeach; ?>
</body>
</html>