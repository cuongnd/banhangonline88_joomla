<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$doc = JFactory::getDocument();
JHtml::_('jQuery.help_step');
JHtml::_('jQuery.auo_typing_text');
JHtml::_('jqueryfrontend.notify');
JHtml::_('jqueryfrontend.uisortable');
JHtml::_('jqueryfrontend.css3_animate_it');
JHtml::_('jqueryfrontend.OwlCarousel');
JHtml::_('jqueryfrontend.serialize_object');
JHtml::_('jqueryfrontend.animate');
JHtml::_('jqueryfrontend.animate_wow');
JHtml::_('jqueryfrontend.lazyYT');
$doc->addScript('/components/com_hikashop/assets/js/view_user_affiliateform.min.js');
$doc->addLessStyleSheet('/components/com_hikashop/assets/less/view_user_affiliateform.less');
global $Itemid;
$url_itemid = '';
if (!empty($Itemid)) {
    $url_itemid = '&Itemid=' . $Itemid;
}
$session = JFactory::getSession();
$userClass = hikashop_get('helper.user');
$key_user_dont_show_help = $userClass::KEY_DONT_SHOW_HELP;
$user_dont_show_help = $session->get($key_user_dont_show_help, 1);

?>
    <div class="view-user-affiliateform">
        <!-- Set up your HTML -->
        <section class="at-slide-block">
            <div id="carousel-example-generic" class="carousel slide carousel-fade" data-interval="4000">

                <div class="carousel-inner white-text" role="listbox">
                    <div class="item active bk slide-1 carousel-caption">
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="slide-info text-center">
                                        <h3 class="animated bounceInUp">Mạng lưới Affiliate hiệu quả và uy tín</h3>
                                        <p class="animated bounceInUp" >Hơn 50,000 Publisher kiếm tiền online hiệu quả cùng banhangonline88.com từ hơn 50 chiến dịch hấp dẫn của các nhà cung cấp hàng đầu Việt Nam.</p>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12">
                                    <div class="slide-img text-center animated bounceInUp" >
                                        <img class="slide1-images img1 animated pulse"  src="/images/stories/home-3-slide-2-layer-21.png" alt="banhangonline88.com home slide image 1 1">
                                        <img class="slide1-images img2" src="/images/stories/home-3-slide-2-layer-20.png" alt="banhangonline88.com home slide image 1 2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item bk slide-6 carousel-caption">
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="slide-info text-center">
                                        <h3 class="animated bounceInUp" >Nền tảng tiếp thị liên kết số 1 tại Việt Nam</h3>
                                        <p class="animated bounceInUp" >Với hơn 16 năm kinh nghiệm phát triển tại Việt Nam và Đông Nam Á. banhangonline88.com là đơn vị tiên phong về tiếp thị liên kết tại Việt Nam.</p>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12">
                                    <div class="slide-img text-center">
                                        <img src="/images/stories/home-7-slide-2-layer-41a.png"  class="slide1-images2 img1 animated bounceInUp" alt="banhangonline88.com home slide image 2 1">
                                        <img src="/images/stories/home-7-slide-2-layer-42a.png"  class="slide1-images2 img2 animated bounceInUp" alt="banhangonline88.com home slide image 2 2">
                                        <img src="/images/stories/home-7-slide-2-layer-43a.png"  class="slide1-images2 img3 animated bounceInUp" alt="banhangonline88.com home slide image 2 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item bk slide-3 carousel-caption">
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="slide-info text-center">
                                        <h3 class="animated bounceInUp" >Chính sách thanh toán tốt nhất thị trường</h3>
                                        <p class="animated bounceInUp" >Chính sách thanh toán hoa hồng siêu tốc, sớm nhất thị trường và cam kết đúng hẹn vào ngày 15 hàng tháng chỉ có tại banhangonline88.com Việt Nam.</p>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12">
                                    <div class="slide-img text-center animated bounceInUp" >
                                        <img class="slide-img0" style="" src="/images/stories/landing-slide-2-layer-1a.png" alt="banhangonline88.com home slide image 3 1">
                                        <img  class="slide2-img animated bounceInRight" src="/images/stories/landing-slide-2-layer-1b.png" alt="banhangonline88.com home slide image 3 2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>

                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left icon-arrow-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right icon-arrow-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>
        <section class="content-block1">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="title-block text-center ">
                            <h3 class="section-title">3 bước kiếm tiền cùng banhangonline88.com</h3>
                        </div>
                    </div>
                    <div class="process-inner clearfix">
                        <div class="col-xs-12 col-sm-6 col-md-4 text-center">
                            <div data-wow-delay="0s" class="process-item-holder wow fadeInUpBig" >
                                <div class="image-holder">
                                    <img title="Proccess 5" alt="Proccess image 5" src="/images/stories/process-image-5.png">
                                </div>
                                <div class="content-holder">
                                    <h6 class="pi-title">Tạo link</h6>
                                    <p>Tạo link tracking chiến dịch</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4 text-center">
                            <div data-wow-delay="0.2s" class="process-item-holder wow fadeInUpBig" >
                                <div class="image-holder">
                                    <img title="Proccess 8" alt="Proccess image 8" src="/images/stories/process-image-8.png">
                                </div>
                                <div class="content-holder">
                                    <h6 class="pi-title">Quảng bá link</h6>
                                    <p>Chia sẻ link thông qua website, blog, mạng xã hội, digital marketing</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4 text-center">
                            <div data-wow-delay="0.4s" class="process-item-holder wow fadeInUpBig" >
                                <div class="image-holder">
                                    <img title="Proccess 6" alt="Proccess image 6" src="/images/stories/process-image-6.png">
                                </div>
                                <div class="content-holder">
                                    <h6 class="pi-title">Nhận hoa hồng</h6>
                                    <p>Nhận hoa hồng từ mỗi đơn hàng, form đăng ký, điền thông tin thành công</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                        <div style="margin-top: 40px;" class="">
                            <a href="https://banhangonline88.com.vn/huong-dan-publisher" class="btn at-btn outline-blue">Tìm hiểu thêm</a>
                        </div>
                    </div>


                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="lazyYT" data-youtube-id="Uli_T6Epqnk" data-ratio="16:9"></div>
            </div>
        </div>
        <section class="content-block2 bk-blue white-text">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="title-block text-center ">
                            <h3 class="section-title">TẠI SAO NÊN CHỌN BANHANGONLINE88.COM</h3>
                            <p class="section-subtitle"></p>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div data-wow-delay="0" class="icon-tiny wow fadeInUpBig" >
                            <div class="icon-tiny-left">
                            <span class="icon-bk">
                            <i aria-hidden="true" class="icon ti-money"></i>
                        </span>
                            </div>
                            <div class="icon-tiny-right">
                                <h6 style="" class="pi-title">Hoa hồng chia sẻ hấp dẫn</h6>
                                <p>Hoa hồng lên tới 21%, thanh toán nhanh nhất vào ngày 15 hàng tháng</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div data-wow-delay="0.2s" class="icon-tiny wow fadeInUpBig" >
                            <div class="icon-tiny-left">
                            <span class="icon-bk">
                            <i aria-hidden="true" class="icon ti-layers"></i>
                        </span>
                            </div>
                            <div class="icon-tiny-right">
                                <h6 style="" class="pi-title">Đa Dạng Chiến Dịch</h6>
                                <p>Hơn 50 chiến dịch trong các lĩnh vực Thương mại điện tử, Du lịch, Ngân hàng &ndash; Bảo hiểm, Làm đẹp…</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div data-wow-delay="0.4s" class="icon-tiny wow fadeInUpBig" >
                            <div class="icon-tiny-left">
                            <span class="icon-bk">
                            <i aria-hidden="true" class="icon ti-stats-up"></i>
                        </span>
                            </div>
                            <div class="icon-tiny-right">
                                <h6 style="" class="pi-title">Hệ Thống Thông Minh</h6>
                                <p>Cập nhật đơn hàng theo thời gian thực, chi tiết đến từng sản phẩm, ngành hàng, chiến dịch</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div data-wow-delay="0s" class="icon-tiny wow fadeInUpBig" >
                            <div class="icon-tiny-left">
                            <span class="icon-bk">
                            <i class="icon-bulb"></i>
                        </span>
                            </div>
                            <div class="icon-tiny-right">
                                <h6 style="" class="pi-title">Luật Tính Hoa Hồng Ưu Việt</h6>
                                <p>Last click và không ghi đè Cookie, Retargeting của Advertiser không ảnh hưởng tới kết quả của Publisher</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div data-wow-delay="0.2s" class="icon-tiny wow fadeInUpBig" >
                            <div class="icon-tiny-left">
                            <span class="icon-bk">
                            <i aria-hidden="true" class="icon ti-reload"></i>
                        </span>
                            </div>
                            <div class="icon-tiny-right">
                                <h6 style="" class="pi-title">Tính Chuyển Đổi Liên Hoàn</h6>
                                <p>Áp dụng cơ chế re-occurred, ghi nhận tất cả chuyển đổi thành công trong thời gian lưu cookie</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div data-wow-delay="0.4s" class="icon-tiny wow fadeInUpBig" >
                            <div class="icon-tiny-left">
                            <span class="icon-bk">
                            <i class="icon-user"></i>
                        </span>
                            </div>
                            <div class="icon-tiny-right">
                                <h6 style="" class="pi-title">Công Nghệ Nhật Bản Uy Tín</h6>
                                <p>Hơn 15 năm kinh nghiệm triển khai thành công tại nhiều quốc gia như Nhật Bản, Thái Lan, Indonesia…</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="register">
                    <h3 class="section-title">Đăng ký ngay hôm nay</h3>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-12">
                            <form class="wow fadeInUpBig" action="<?php echo hikashop_completeLink('user&task=register' . $url_itemid); ?>" method="post"
                                  name="hikashop_registration_form" enctype="multipart/form-data"
                                  onsubmit="hikashopSubmitForm('hikashop_registration_form'); return false;">
                                <div class="hikashop_user_registration_page">
                                    <div class="list-btn-social">
                                        <h2><?php echo JText::_('REGISTER_BY_SOCIAL_ACCOUNT') ?></h2>
                                        <?php
                                        $fb = JFactory::getFaceBook();
                                        $helper = $fb->getRedirectLoginHelper();
                                        $permissions = ['public_profile', 'email']; // Optional permissions
                                        $task_create_user_by_facebook = JUri::root() . 'index.php?option=com_hikashop&ctrl=user&task=active_user_partner_activated_by_current_user_login_by_facebook';
                                        $loginUrl = $helper->getLoginUrl($task_create_user_by_facebook, $permissions);
                                        ?>
                                        <a target="_self" href="<?php echo $loginUrl ?>" class="btn btn-facebook"><i
                                                class="fa fa-facebook"></i> | <?php echo JText::_('REGISTER_BY_ACOUNT_FACEBOOK') ?></a>
                                        <a target="_self" href="javascript:void(0)" class="get-google-plus-login btn btn-google-plus"><i
                                                class="fa fa-google-plus"></i> | <?php echo JText::_('REGISTER_BY_ACOUNT_GOOGLE') ?></a>
                                    </div>


                                    <fieldset class="input">
                                        <h2><?php echo JText::_('HIKA_REGISTRATION'); ?></h2>
                                        <?php
                                        $this->setLayout('registration');
                                        $this->registration_page = true;
                                        $this->form_name = 'hikashop_registration_form';
                                        $usersConfig = JComponentHelper::getParams('com_users');
                                        $allowRegistration = $usersConfig->get('allowUserRegistration');
                                        if ($allowRegistration || $this->simplified_registration == 2) {
                                            echo $this->loadTemplate();
                                        } else {
                                            echo JText::_('REGISTRATION_NOT_ALLOWED');
                                        }
                                        ?>
                                    </fieldset>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php
$list_messenger = array();
$key = "HIKA_NAME_REQUIRED";
$list_messenger[$key] = JText::_($key);

$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("body.site.com_hikashop.view-user.layout-affiliateform.task-affiliateform").view_user_affiliateform({
                list_messenger:<?php echo json_encode($list_messenger) ?>,
                user_dont_show_help:<?php echo (int)$user_dont_show_help ?>,
                key_user_dont_show_help: "<?php echo $key_user_dont_show_help ?>"
            });
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>