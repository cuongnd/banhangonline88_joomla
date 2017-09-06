<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
// Include main engine
$doc = JFactory::getDocument();
JHtml::_('jQuery.help_step');
JHtml::_('jQuery.auo_typing_text');
JHtml::_('jqueryfrontend.notify');
JHtml::_('jqueryfrontend.uisortable');
JHtml::_('jqueryfrontend.ui_dialog');
JHtml::_('jqueryfrontend.serialize_object');
JHtml::_('jqueryfrontend.lazyYT');


$doc->addScript('/components/com_hikamarket/assets/js/view_vendormarket_form.min.js');
$doc->addLessStyleSheet('/components/com_hikamarket/assets/less/view_vendormarket_form.less');
$file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
jimport('joomla.filesystem.file');
if (!JFile::exists($file)) {
    return;
}
require_once($file);
$language = JFactory::getLanguage();
$language->load('com_easysocial');
$language->load('mod_easysocial_login');
$modules = FD::modules('mod_easysocial_login');
// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
// We need these packages
$modules->addDependency('css', 'javascript');

// Include the engine file.
$session = JFactory::getSession();
$vendorClass = hikamarket::get('helper.vendor');
$key_user_dont_show_help = $vendorClass::KEY_DONT_SHOW_HELP;
$user_dont_show_help = $session->get($key_user_dont_show_help, 1);
$facebook = FD::oauth('Facebook');
?><?php
if (empty($this->form_type))
    $this->form_type = 'vendor';
?>
    <div class="view-form-vendor">
        <div class="home-wrapper">
            <div class="home-banner">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <h1>Nền tảng website bán hàng được <br>sử dụng nhiều nhất Việt Nam</h1>
                            <p class="desc">
                                Hãy dùng cách <b>30,000+</b> doanh nghiệp và chủ shop đã chọn
                            </p>
                            <a
                                href="javascript:;" class="btn-registration">Nào bắt đầu dùng thôi</a>
                        </div>
                        <div class="col-lg-4 block-image hidden visible-lg">
                            <img alt="banhangonline88.com"
                                 src="/images/stories/image_register_vendor_page/img-home-banner.png">
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="javascript:;" class="scroll-down faa-bounce animated">
                            <i class="ti-angle-down"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="lazyYT" data-youtube-id="5FQIBiiUnvc" data-ratio="16:9"></div>
                </div>
            </div>

            <div class="home-omnichannel">
                <div class="container">
                    <h2>banhangonline88.com Omnichannel <span class="hidden-xs">-</span> <br class="hidden visible-xs">Website
                        bán hàng đa kênh</h2>
                    <p class="desc">
                        Giúp bạn bán hàng khắp mọi nơi, tăng doanh số <br class="hidden visible-sm visible-xs">và quản
                        lý tập trung một chỗ
                    </p>
                    <div class="omni-chart hidden-xs">
                        <img alt="banhangonline88.com Omnichannel"
                             src="/images/stories/image_register_vendor_page/img-bizweb-omnichanel.png"
                             class="hidden visible-lg">
                        <img alt="banhangonline88.com Omnichannel"
                             src="/images/stories/image_register_vendor_page/img-bizweb-omnichanel-tablet.png"
                             class="hidden-lg">
                        <div class="channel offline">
                            <a href="/#" target="_blank">
                                <i class="channel-icon icon-channel-offline"></i>
                                <div class="info">
                                    <h3>Bán tại <b>Cửa hàng</b></h3>
                                    <p>Kết nối bán hàng online và offline, <br>quản lý chuỗi cửa hàng, tồn kho.</p>
                                </div>
                            </a>
                        </div>
                        <div class="channel facebook">
                            <a href="#" target="_blank">
                                <i class="channel-icon icon-channel-facebook"></i>
                                <div class="info">
                                    <h3>Bán trên <i class="icon-info-facebook"></i></h3>
                                    <p>Bán hàng chuyên nghiệp và hiệu quả <br>trên mạng xã hội lớn nhất thế giới.</p>
                                </div>
                            </a>
                        </div>
                        <div class="channel zalo">
                            <a href="#" target="_blank">
                                <i class="channel-icon icon-channel-zalo"></i>
                                <div class="info">
                                    <h3>Bán trên <i class="icon-info-zalo"></i></h3>
                                    <p>Bán trên ứng dụng chat phổ biến <br>với +70 triệu người dùng.</p>
                                </div>
                            </a>
                        </div>
                        <div class="channel ecommerce">
                            <a href="/ban-hang-tren-lazada.html" target="_blank">
                                <i class="channel-icon icon-channel-ecommerce"></i>
                                <div class="info">
                                    <h3>Bán trên <b>3 Sàn TMĐT</b></h3>
                                    <p>Tiếp cận lượng khách hàng khổng lồ, <br>kết nối đồng bộ nhanh chóng.</p>
                                </div>
                            </a>
                            <div class="logo-ecommerce">
                                <img alt="banhangonline88.com"
                                     src="/images/stories/image_register_vendor_page/img-logo-ecommerce.png">
                            </div>
                        </div>
                        <div class="channel website">
                            <a href="#" target="_blank">
                                <i class="channel-icon icon-channel-website"></i>
                                <div class="info">
                                    <h3>Bán trên <b>Website</b></h3>
                                    <p>Hơn 10,000 chủ shop cho rằng <br>website là kênh bán hàng hiệu quả nhất.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="omni-chart-mobile hidden visible-xs">
                        <ul>
                            <li>
                                <img alt="banhangonline88.com Omnichannel"
                                     src="/images/stories/image_register_vendor_page/img-bizweb-omnichanel-mobile.svg">
                            </li>
                            <li class="offline">
                                <a href="#" target="_blank">
                                    <i class="icon-channel-offline"></i>
                                    <div class="info">
                                        <h3>Bán tại <b>Cửa hàng</b></h3>
                                        <p>Kết nối bán hàng online và offline, quản lý chuỗi cửa hàng, tồn kho.</p>
                                    </div>
                                </a>
                            </li>
                            <li class="facebook">
                                <a href="#" target="_blank">
                                    <i class="channel-icon icon-channel-facebook"></i>
                                    <div class="info">
                                        <h3>Bán trên <i class="icon-info-facebook"></i></h3>
                                        <p>Bán hàng chuyên nghiệp và hiệu quả trên mạng xã hội lớn nhất thế giới..</p>
                                    </div>
                                </a>
                            </li>
                            <li class="zalo">
                                <a href="#" target="_blank">
                                    <i class="channel-icon icon-channel-zalo"></i>
                                    <div class="info">
                                        <h3>Bán trên <i class="icon-info-zalo"></i></h3>
                                        <p>Bán trên ứng dụng chat phổ biến với +70 triệu người dùng.</p>
                                    </div>
                                </a>
                            </li>
                            <li class="ecommerce">
                                <a href="#" target="_blank">
                                    <i class="channel-icon icon-channel-ecommerce"></i>
                                    <div class="info">
                                        <h3>Bán trên <b>3 Sàn TMĐT</b></h3>
                                        <p>Tiếp cận lượng khách hàng khổng lồ, kết nối đồng bộ nhanh chóng.</p>
                                        <div class="logo-ecommerce">
                                            <img height="15" alt="banhangonline88.com"
                                                 src="/images/stories/image_register_vendor_page/img-logo-ecommerce-mobile.png">
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="website">
                                <a href="#" target="_blank">
                                    <i class="channel-icon icon-channel-website"></i>
                                    <div class="info">
                                        <h3>Bán trên <b>Website</b></h3>
                                        <p>Hơn 10.000 chủ shop cho rằng website là kênh bán hàng hiệu quả nhất.</p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <a target="_blank" href="#" class="view-detail">Tìm hiểu thêm về banhangonline88.com
                            Omnichannel</a>
                    </div>
                    <a href="javascript:;" class="scroll-down"></a>
                </div>
            </div>

            <div class="home-security">
                <div class="container">
                    <p class="desc">Website bán hàng banhangonline88.com vượt trội với</p>
                    <h2>Công nghệ bảo mật SSL và tối ưu SEO</h2>
                    <div class="row first">
                        <div class="col-md-6 col-sm-12 block-image pull-right">
                            <img alt="banhangonline88.com SSL"
                                 src="/images/stories/image_register_vendor_page/home-img-security-1.jpg">
                        </div>
                        <div class="col-md-6 col-sm-12 block-info pull-left">
                            <h3><a target="_blank" href="#">Website được bảo mật cao với SSL</a></h3>
                            <ul>
                                <li class="security-sheld">
                                    Xác thực website và bảo mật thông tin trước sự tấn <br class="hidden-sm hidden-xs">công
                                    của virus, hacker
                                </li>
                                <li class="security-trust">
                                    Nâng cao uy tín và mức độ tin tưởng của thương hiệu <br class="hidden-sm hidden-xs">khi
                                    website của bạn có <a target="_blank" href="/#"> chứng chỉ SSL</a>
                                </li>
                                <li class="security-certificate">
                                    Website được cấp chứng chỉ SSL với giao thức HTTPS <br class="hidden-sm hidden-xs">sẽ
                                    được Google đánh giá tốt và hỗ trợ SEO
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row second">
                        <div class="col-md-6 block-image">
                            <img title="" alt="SSL"
                                 src="/images/stories/image_register_vendor_page/home-img-security-2.jpg"/>
                        </div>
                        <div class="col-md-6 block-info">
                            <h3><a target="_blank" href="#">Tối ưu SEO</a></h3>
                            <ul>
                                <li class="security-search">
                                    Website bán hàng banhangonline88.com được tối ưu để thân thiện với <br
                                        class="hidden-sm hidden-xs">các công cụ tìm kiếm, đặc biệt là Google
                                </li>
                                <li class="security-title">
                                    Tối ưu tiêu đề, mô tả SEO cho từng trang nội dung và cơ <br
                                        class="hidden-sm hidden-xs">chế gợi ý tiêu đề, mô tả tự động
                                </li>
                                <li class="security-alt">
                                    Hỗ trợ SEO hình ảnh với thẻ ALT và đặc biệt link URL <br
                                        class="hidden-sm hidden-xs">được tối ưu chuẩn SEO
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <a
                            href="javascript:;" class="btn  btn-registration">Dùng thử miễn phí</a>
                    </div>
                </div>
            </div>

            <div class="new-index-comments">
                <div class="container">
                    <h2 data-wow-delay="0.5s" data-wow-duration="4" class="wow fadeIn">30,000+ khách hàng nói về
                        banhangonline88.com</h2>
                    <p data-wow-delay="0.5s" data-wow-duration="4" class="sub-title wow fadeIn">Sự hài lòng của khách
                        hàng chính là thành công lớn nhất của chúng tôi</p>
                    <div data-wow-delay="0.5s" data-wow-duration="4" class="row row-item wow fadeIn">
                        <div
                            class="col-lg-7 col-md-7 col-sm-7 col-xs-12 col-lg-push-5 col-md-push-5 col-sm-push-5 second-col">
                            <img class="cursor-pointer"
                                 src="/images/stories/image_register_vendor_page/gento-image.png">
                        </div>
                        <div class="col-md-5 col-sm-5 col-xs-12 col-lg-pull-7 col-md-pull-7 col-sm-pull-7 first-col">
                            <div class="swiper-container swiper-container-horizontal" style="cursor: grab;">
                                <div class="swiper-wrapper"
                                     style="transform: translate3d(-1464px, 0px, 0px); transition-duration: 0ms;">
                                    <div
                                        class="swiper-slide gento-slide swiper-slide-duplicate swiper-slide-duplicate-active"
                                        data-swiper-slide-index="2" style="width: 488px;">
                                        <div class="item-comment">
                                            <img src="/images/stories/image_register_vendor_page/open-quote.png"
                                                 class="quote">
                                            <p class="comment">
                                                Website banhangonline88.com rất dễ sử dụng. Tuy không phải là một lập
                                                trình viên chuyên nghiệp nhưng tôi vẫn có thể tự quản trị website của
                                                mình.
                                            </p>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-4">
                                                    <img
                                                        src="/images/stories/image_register_vendor_page/gento-customer.png"
                                                        class="customer-image">
                                                </div>
                                                <div class="col-md-9 col-sm-8 col-xs-8">
                                                    <p class="customer-name">Ông: Nguyễn Minh Đức</p>
                                                    <p class="website"><a target="_blank" rel="nofollow"
                                                                          href="http://gento.vn/">http://gento.vn/</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide bee-slide swiper-slide-duplicate-next"
                                         data-swiper-slide-index="0" style="width: 488px;">
                                        <div class="item-comment">
                                            <img src="/images/stories/image_register_vendor_page/open-quote.png"
                                                 class="quote">
                                            <p class="comment">
                                                banhangonline88.com giúp chúng tôi thay đổi cách bán hàng, không chỉ bán
                                                hàng trực tiếp như trước đây mà còn bán trên website, Facebook và Sendo
                                                nữa. Khách hàng cũng nhiều hơn!
                                            </p>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-4">
                                                    <img
                                                        src="/images/stories/image_register_vendor_page/bee-customer.png"
                                                        class="customer-image">
                                                </div>
                                                <div class="col-md-9 col-sm-8 col-xs-8">
                                                    <p class="customer-name">Bà: Tống Ngọc Ánh</p>
                                                    <p class="website"><a target="_blank" rel="nofollow"
                                                                          href="http://www.beemart.vn">www.beemart.vn</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide kangaroo-slide swiper-slide-prev"
                                         data-swiper-slide-index="1" style="width: 488px;">
                                        <div class="item-comment">
                                            <img src="/images/stories/image_register_vendor_page/open-quote.png"
                                                 class="quote">
                                            <p class="comment">
                                                Là đối tác lâu năm của banhangonline88.com, tôi nhận thấy một phong cách
                                                làm việc chuyên nghiệp, nhiệt tình ở các bạn. Chúc banhangonline88.com
                                                sẽ ngày một phát triển hơn nữa.
                                            </p>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-4">
                                                    <img
                                                        src="/images/stories/image_register_vendor_page/kangaroo-customer.png"
                                                        class="customer-image">
                                                </div>
                                                <div class="col-md-9 col-sm-8 col-xs-8">
                                                    <p class="customer-name">Bà: Ngô Tuyết Nhung </p>
                                                    <p class="website"><a target="_blank" rel="nofollow"
                                                                          href="http://www.kangaroohanoi.vn/">www.kangaroohanoi.vn</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide gento-slide swiper-slide-active"
                                         data-swiper-slide-index="2" style="width: 488px;">
                                        <div class="item-comment">
                                            <img src="/images/stories/image_register_vendor_page/open-quote.png"
                                                 class="quote">
                                            <p class="comment">
                                                Website banhangonline88.com rất dễ sử dụng. Tuy không phải là một lập
                                                trình viên chuyên nghiệp nhưng tôi vẫn có thể tự quản trị website của
                                                mình.
                                            </p>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-4">
                                                    <img
                                                        src="/images/stories/image_register_vendor_page/gento-customer.png"
                                                        class="customer-image">
                                                </div>
                                                <div class="col-md-9 col-sm-8 col-xs-8">
                                                    <p class="customer-name">Ông: Nguyễn Minh Đức</p>
                                                    <p class="website"><a target="_blank" rel="nofollow"
                                                                          href="http://gento.vn/">http://gento.vn/</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide bee-slide swiper-slide-duplicate swiper-slide-next"
                                         style="width: 488px;">
                                        <div class="item-comment">
                                            <img src="/images/stories/image_register_vendor_page/open-quote.png"
                                                 class="quote">
                                            <p class="comment">
                                                banhangonline88.com giúp chúng tôi thay đổi cách bán hàng, không chỉ bán
                                                hàng trực tiếp như trước đây mà còn bán trên website, Facebook và Sendo
                                                nữa. Khách hàng cũng nhiều hơn!
                                            </p>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-4 col-xs-4">
                                                    <img
                                                        src="/images/stories/image_register_vendor_page/bee-customer.png"
                                                        class="customer-image">
                                                </div>
                                                <div class="col-md-9 col-sm-8 col-xs-8">
                                                    <p class="customer-name">Bà: Tống Ngọc Ánh</p>
                                                    <p class="website"><a target="_blank" rel="nofollow"
                                                                          href="http://www.beemart.vn">www.beemart.vn</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="horizontal">
                                <div class="swiper-button-prev">
                                    <i class="fa fa-chevron-left"></i>
                                </div>
                                <div class="swiper-button-next">
                                    <i class="fa fa-chevron-right"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="typical-customers">
                        <div class="row hidden-xs">
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Sao Thái Dương"
                                         src="/images/stories/image_register_vendor_page/logo-saothaiduong.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Alphabooks"
                                         src="/images/stories/image_register_vendor_page/logo-alphabooks.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Eva Shoes"
                                         src="/images/stories/image_register_vendor_page/logo-evashoes.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Ugether"
                                         src="/images/stories/image_register_vendor_page/logo-ugether.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Vietnam Post"
                                         src="/images/stories/image_register_vendor_page/logo-vnpost.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="TCL" src="/images/stories/image_register_vendor_page/logo-tcl.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Bé bụ bẫm"
                                         src="/images/stories/image_register_vendor_page/logo-bebubam.png">
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-3 customer-item">
                                <a rel="nofollow" target="_blank" href="#">
                                    <img alt="Chu Đậu" src="/images/stories/image_register_vendor_page/logo-chudau.png">
                                </a>
                            </div>
                        </div>

                        <div class="slide-mobile hidden visible-xs">
                            <div class="swiper-container swiper-container-horizontal">
                                <div class="swiper-wrapper" style="transition-duration: 0ms;">
                                    <div class="swiper-slide swiper-slide-duplicate">
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Bé bụ bẫm"
                                                     src="/images/stories/image_register_vendor_page/logo-bebubam.png">
                                            </a>
                                        </div>
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Chu Đậu"
                                                     src="/images/stories/image_register_vendor_page/logo-chudau.png">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide swiper-slide-prev" data-swiper-slide-index="0">
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Sao Thái Dương"
                                                     src="/images/stories/image_register_vendor_page/logo-saothaiduong.png">
                                            </a>
                                        </div>
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Alphabooks"
                                                     src="/images/stories/image_register_vendor_page/logo-alphabooks.png">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide swiper-slide-active" data-swiper-slide-index="1">
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Eva Shoes"
                                                     src="/images/stories/image_register_vendor_page/logo-evashoes.png">
                                            </a>
                                        </div>
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Ugether"
                                                     src="/images/stories/image_register_vendor_page/logo-ugether.png">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide swiper-slide-next" data-swiper-slide-index="2">
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Vietnam Post"
                                                     src="/images/stories/image_register_vendor_page/logo-vnpost.png">
                                            </a>
                                        </div>
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="TCL"
                                                     src="/images/stories/image_register_vendor_page/logo-tcl.png">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide" data-swiper-slide-index="3">
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="#">
                                                <img alt="Bé bụ bẫm"
                                                     src="/images/stories/image_register_vendor_page/logo-bebubam.png">
                                            </a>
                                        </div>
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="https://www.chudauceramic.shop/">
                                                <img alt="Chu Đậu"
                                                     src="/images/stories/image_register_vendor_page/logo-chudau.png">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide swiper-slide-duplicate swiper-slide-duplicate-prev"
                                         data-swiper-slide-index="0">
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="https://saothaiduong.com.vn/">
                                                <img alt="Sao Thái Dương"
                                                     src="/images/stories/image_register_vendor_page/logo-saothaiduong.png">
                                            </a>
                                        </div>
                                        <div class="customer-item">
                                            <a rel="nofollow" target="_blank" href="https://alphabooks.vn/">
                                                <img alt="Alphabooks"
                                                     src="/images/stories/image_register_vendor_page/logo-alphabooks.png">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-pagination swiper-pagination-bullets"><span
                                        class="swiper-pagination-bullet"></span><span
                                        class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span
                                        class="swiper-pagination-bullet"></span><span
                                        class="swiper-pagination-bullet"></span></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="news-aboutus">
                <div class="container">
                    <h2 data-wow-delay="0.5s" data-wow-duration="4" class="wow fadeIn"><a href="/#">Báo chí nói về
                            banhangonline88.com</a></h2>
                    <div data-wow-delay="0.5s" data-wow-duration="4" class="row wow fadeIn">
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 item">
                            <div class="news-item">
                                <a href="#"><img
                                        src="/images/stories/image_register_vendor_page/thong-bao-thay-doi-menu-he-quan-tri-bizweb.jpg"

                                        class=""></a>
                                <div class="row">
                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                        <ul>
                                            <li>
                                                <div class="published-date">27</div>
                                            </li>
                                            <li>
                                                <div class="published-dayofweek">Th 7</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-9 col-sm-9 col-xs-9 article-col">
                                        <a target="_blank" class="article-title" href="#">
                                            Chớp ngay cơ hội đặc biệt này để được làm web giá hời với
                                            banhangonline88.com
                                        </a>
                                    </div>
                                </div>
                                <span class="source-name">Cafebiz</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 item">
                            <div class="news-item">
                                <a href="#"><img
                                        src="/images/stories/image_register_vendor_page/cong-ty-thiet-ke-website-uy-tin.jpg"

                                        class=""></a>
                                <div class="row">
                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                        <ul>
                                            <li>
                                                <div class="published-date">26</div>
                                            </li>
                                            <li>
                                                <div class="published-dayofweek">Th 7</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-9 col-sm-9 col-xs-9 article-col">
                                        <a target="_blank" class="article-title"
                                           href="#">
                                            banhangonline88.com tung khuyến mại combo làm web đặc biệt mừng sinh nhật 9
                                            tuổi
                                        </a>
                                    </div>
                                </div>
                                <span class="source-name">Vnreview</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 item">
                            <div class="news-item">
                                <a href="#"><img
                                        src="/images/stories/image_register_vendor_page/3.jpg"
                                        class=""></a>
                                <div class="row">
                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                        <ul>
                                            <li>
                                                <div class="published-date">26</div>
                                            </li>
                                            <li>
                                                <div class="published-dayofweek">Th 7</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-9 col-sm-9 col-xs-9 article-col">
                                        <a target="_blank" class="article-title" href="#">
                                            Giải thưởng Nhân tài đất Việt với Công nghệ sáng tạo, Kết nối thông minh
                                        </a>
                                    </div>
                                </div>
                                <span class="source-name">VnMedia</span>
                            </div>
                        </div>
                    </div>
                    <a class="see-more"
                       href="#">Xem thêm báo
                        chí nói về banhangonline88.com</a>
                </div>
            </div>
            <div class="bw-partner hidden-xs">
                <div class="container">
                    <ul>
                        <li><a href="#" target="_blank"><img
                                    src="/images/stories/image_register_vendor_page/logo_Dantri_Mau.png"></a>
                        </li>
                        <li class="cfbiz"><a href="#" rel="nofollow" target="_blank"><img
                                    src="/images/stories/image_register_vendor_page/cafebiz-logo.png"></a></li>
                        <li><a href="#" target="_blank"><img
                                    src="/images/stories/image_register_vendor_page/logo_vtv_mau.png"></a>
                        </li>
                        <li><a href="#" rel="nofollow" target="_blank"><img
                                    src="/images/stories/image_register_vendor_page/logo_VietnamPlus_mau.png"></a></li>
                        <li><a href="#" target="_blank"><img
                                    src="/images/stories/image_register_vendor_page/logo_vnxpress_mau.png"></a></li>
                        <li><a href="#" target="_blank"><img
                                    src="/images/stories/image_register_vendor_page/logo_ictnews_mau.png"></a></li>
                    </ul>
                </div>
            </div>
            <div class="bw-staff">
                <div class="container">
                    <h2 data-wow-delay="0.5s" data-wow-duration="3" class="wow fadeIn">Làm cho việc bán hàng trở nên dễ
                        dàng</h2>
                    <p data-wow-delay="0.5s" data-wow-duration="3" class="sub-title wow fadeIn">Chúng tôi bắt đầu sứ
                        mệnh này từ năm 2008</p>
                    <div data-wow-delay="0.5s" data-wow-duration="3" class="row wow fadeIn">
                        <div class="col-md-4 col-sm-4 col-xs-6 item-col">
                            <img src="/images/stories/image_register_vendor_page/single-people.png">
                            <hr>
                            <p>+500</p>
                            <span>Nhân viên</span>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6 item-col">
                            <img src="/images/stories/image_register_vendor_page/people.png">
                            <hr>
                            <p>+30,000</p>
                            <span>Khách hàng</span>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12 item-col">
                            <img src="/images/stories/image_register_vendor_page/handshake.png">
                            <hr>
                            <p>+40</p>
                            <span>Đối tác</span>
                        </div>
                    </div>
                    <p class="hotline">Hotline <strong>024 66867806</strong></p>
                </div>
            </div>
            <div class="block-register">
                <div class="container">
                    <h3>Hơn 30,000 doanh nghiệp và chủ shop đang bán hàng như thế nào ?</h3>
                    <span class="sub-text">Đăng ký dùng thử banhangonline88.com miễn phí 15 ngày để khám phá</span>
                    <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 button-block">
                        <input type="text" placeholder="Nhập tên cửa hàng/doanh nghiệp của bạn" value=""
                               class="input-site-name hidden-xs" id="site_name_bottom">
                        <a href="javascript:;"
                           class="btn-registration banner-home-registration event-banhangonline88.com-Free-Trial-form-open">Dùng
                            thử miễn phí</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-register">

            <div class="row">
                <div class="col-lg-12">
                    <div class="pull-right">
                        <a class="btn btn-link help"><i class="glyphicon glyphicon-question-sign"></i></a>
                    </div>
                </div>
            </div>
            <?php
            if ($this->form_type == 'vendorregister') {
                ?>
                <form id="hikamarket_registration_form" name="hikamarket_registration_form" method="post"
                      action="<?php echo hikamarket::completeLink('vendor&task=register' . $this->url_itemid); ?>"
                      enctype="multipart/form-data" class="form-horizontal"
                >
                    <div class="list-btn-social">
                        <h2><?php echo JText::_('REGISTER_BY_SOCIAL_ACCOUNT') ?></h2>
                        <?php
                        $fb = JFactory::getFaceBook();
                        $helper = $fb->getRedirectLoginHelper();
                        $permissions = ['public_profile', 'email']; // Optional permissions
                        $task_create_user_by_facebook = JUri::root() . 'index.php?option=com_hikamarket&ctrl=vendor&task=create_account_vendor_current_user_login_by_facebook';
                        $loginUrl = $helper->getLoginUrl($task_create_user_by_facebook, $permissions);
                        ?>
                        <a target="_self" href="<?php echo $loginUrl ?>" class="btn btn-facebook"><i
                                class="fa fa-facebook"></i> | <?php echo JText::_('REGISTER_BY_ACOUNT_FACEBOOK') ?></a>
                        <a target="_self" href="javascript:void(0)" class="get-google-plus-login btn btn-google-plus"><i
                                class="fa fa-google-plus"></i> | <?php echo JText::_('REGISTER_BY_ACOUNT_GOOGLE') ?></a>
                    </div>
                    <div class="hikamarket_vendor_registration_page">
                        <h1><?php echo JText::_('HIKA_VENDOR_REGISTRATION'); ?></h1>
                        <?php
                        $this->setLayout('registration');
                        echo $this->loadTemplate();
                        ?>
                        <input type="hidden" name="task" value="register"/>
                        <input type="hidden" name="ctrl" value="vendor"/>
                        <input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
                    </div>
                </form>
                <?php
            } else {
                ?>
                <form id="hikamarket_vendor_form" name="hikamarket_vendor_form" method="post"
                      action="<?php echo hikamarket::completeLink('vendor&task=form' . $this->url_itemid); ?>"
                      enctype="multipart/form-data" class="form-horizontal">
                    <div class="hikamarket_vendor_edit_page">
                        <h1><?php echo JText::_('HIKAM_VENDOR_EDIT'); ?></h1>
                        <?php
                        if (hikamarket::acl('vendor/edit')) {
                            $this->setLayout('registration');
                            echo $this->loadTemplate();
                        }

                        if (hikamarket::acl('vendor/edit/users')) {
                            $this->setLayout('users');
                            echo $this->loadTemplate();
                        }
                        ?>
                        <input type="hidden" name="vendor_id" value="<?php echo $this->element->vendor_id; ?>"/>
                        <input type="hidden" name="task" value="save"/>
                        <input type="hidden" name="ctrl" value="vendor"/>
                        <input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
                    </div>
                </form>

                <?php
            }
            ?>
        </div>
    </div>
<?php
$list_messenger = array();
$key = "HIKA_NAME_REQUIRED";
$list_messenger[$key] = JText::_($key);
$key = "HIKA_EMAIL_REQUIRED";
$list_messenger[$key] = JText::_($key);
$key = "HIKA_PASSWORD_REQUIRED";
$list_messenger[$key] = JText::_($key);
$key = "HIKA_PASSWORD_RETYPE_REQUIRED";
$list_messenger[$key] = JText::_($key);
$key = "HIKA_EMAIL_INCORRECT";
$list_messenger[$key] = JText::_($key);
$key = "HIKA_PASSWORD_RETYPE_INCORRECT";
$list_messenger[$key] = JText::_($key);
$key = "HIKA_VENDOR_NAME_REQUIRED";
$list_messenger[$key] = JText::_($key);
$key = "HIKAM_ERR_TERMS_EMPTY";
$list_messenger[$key] = JText::_($key);

$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("body.site.com_hikamarket.view-vendormarket.layout-form").view_vendormarket_form({
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