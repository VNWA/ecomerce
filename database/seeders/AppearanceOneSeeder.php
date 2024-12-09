<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appearance;

class AppearanceOneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appearance::truncate();


        Appearance::create([
            'type' => 'logo',
            'value' => [
                'logo_full' => url('/images/vnwaLogoFull.png'),
                'logo_icon' => url('/images/vnwaLogoIcon.png'),
                'favicon' => url('/images/vnwaLogoIcon.png'),
            ],
        ]);

        Appearance::create([
            'type' => 'top_nav',
            'value' => [
                [
                    'name' => 'Preguntas & Respuestas',
                    'link' => '/'
                ],
                [
                    'name' => 'Ideas para regalo',
                    'link' => '/'
                ],

            ],
        ]);
        Appearance::create([
            'type' => 'footer',
            'value' => [
                'footer_sidebar' =>
                    [
                        [
                            'name' => 'INFORMACIÓN',
                            'value' => [
                                [
                                    'name' => 'Contacto',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Devoluciones y reclamaciones',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Seguimiento de pedido',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Pago y envíos',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Press',
                                    'link' => '/'
                                ],
                            ]
                        ],
                        [
                            'name' => 'TIENDA ONLINE',
                            'value' => [
                                [
                                    'name' => 'Condiciones de uso',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Protección de datos',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Reglamentos',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Tiempos de secado',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Compara bases semipermanentes',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Inspírate',
                                    'link' => '/'
                                ],
                                [
                                    'name' => 'Configuración de cookies',
                                    'link' => '/'
                                ],
                            ]
                        ],

                    ],
                'footer_express' => [],
                'footer_payment_methods' => [],
                'footer_bottom' => '<p><span class=\"text-small\" style=\"color:hsl(0,0%,0%);\"><strong>CÔNG TY TNHH VINAWEBAPP.COM</strong> &nbsp;GPĐKKD số: </span><span class=\"text-small\" style=\"background-color:rgb(255,255,255);color:rgb(33,37,41);font-family:Arial, Helvetica, sans-serif;\"><strong>0402211956&nbsp;</strong></span><span class=\"text-small\" style=\"color:hsl(0,0%,0%);\"> Người đại diện: Nguyễn Phan Nhật Nguyên</span><br><br><br><span class=\"text-small\" style=\"color:hsl(0,0%,0%);\">VINAWEBAPP.COM @2015 - 2024 – VINAWEBAPP.COM là công ty hàng đầu tại Việt Nam trong lĩnh vực phát triển phần mềm, cung cấp các giải pháp công nghệ tiên tiến cho doanh nghiệp. Chúng tôi mang đến sự tiện lợi, tối ưu hóa quy trình kinh doanh và giúp khách hàng nâng cao năng lực cạnh tranh thông qua các sản phẩm phần mềm chất lượng cao. Với trụ sở chính tại Đà Nẵng và chi nhánh tại TP.HCM, Hà Nội, Bình Dương, chúng tôi hỗ trợ và cung cấp dịch vụ trên khắp cả nước, bao gồm các tỉnh thành như Bà Rịa Vũng Tàu, Bình Phước, Đồng Nai, Tây Ninh, An Giang, Bạc Liêu, Bến Tre, Cà Mau, Cần Thơ, Đồng Tháp, Hậu Giang, Kiên Giang, Long An, Sóc Trăng, Tiền Giang, Bình Định, Bình Thuận, Đà Nẵng, Khánh Hòa, Ninh Thuận, Phú Yên, Quảng Nam, Quảng Ngãi, Trà Vinh, Vĩnh Long, Đắk Lắk, Đắk Nông, Gia Lai, Kon Tum, Lâm Đồng, Bắc Ninh, Hà Nam, Hải Dương, Hải Phòng, Hưng Yên, Nam Định, Ninh Bình, Thái Bình, Vĩnh Phúc, Điện Biên, Hòa Bình, Lai Châu, Lào Cai, Sơn La, Yên Bái, Bắc Giang, Bắc Kạn, Cao Bằng, Hà Giang, Lạng Sơn, Phú Thọ, Quảng Ninh, Thái Nguyên, Tuyên Quang, Hà Tĩnh, Nghệ An, Quảng Bình, Quảng Trị, Thanh Hóa, Thừa Thiên Huế. VINAWEBAPP.COM luôn đồng hành cùng khách hàng trong hành trình chuyển đổi số, mang lại sự khác biệt và hiệu quả cao. DMCA.com Protection Status.. </span><a href=\"https://www.dmca.com/Protection/Status.aspx?ID=27c7bd2a-af6a-40b8-87f5-498526e3ff8f&amp;refurl=https://www.tdm.vn/bon-cau-bet-ve-sinh\"><span class=\"text-small\" style=\"color:hsl(0,0%,0%);\"></span></a></p>',
            ],
        ]);
        Appearance::create([
            'type' => 'profile',
            'value' => [
                'contact' => [
                    'facebook' => '/',
                    'instagram' => '/',
                    'youtube' => '/',
                    'pinterest' => '/',
                    'tiktok' => '/',
                    'whatsapp' => '/',
                    'phone' => '0123456789',
                    'email' => 'example@gmail.com',
                ],
                'seo' => [
                    'meta_image' => url('/images/vnwaLogoIcon.png'),
                    'meta_desc' => 'Cuối cùng bạn đã đến. Sẵn sàng cho một trang web mới đẹp và hiệu quả? Chúng tôi,nhóm thiết kế web chuyên nghiệp của bạn, đang chờ bạn yêu cầu.',
                    'meta_title' => 'Vinawebapp.com',
                ]
            ],
        ]);
        Appearance::create([
            'type' => 'homeSections',
            'value' => [
                'SectionProduct' => [
                    'name' => 'Kits with lamp for beginners',
                    'desc' => 'Start your adventure with semi-permanent nail polishes with our kit and create your beauty salon at home.',
                    'image' => 'http://localhost:3000/_ipx/s_800x450/images/23.webp',
                    'products' => []
                ],
                'SectionColor' => [
                    'name' => 'Choose Your UV Polish Category',
                    'items' => [
                        [
                            'name' => 'Semi-permanent Nail Polish 3 steps',
                            'image' => '/images/24.webp',
                            'desc' => 'Essential and premium base',
                            'link' => '/',
                        ],
                        [
                            'name' => 'Semi-permanent Nail Polish 3 steps',
                            'image' => '/images/24.webp',
                            'desc' => 'Essential and premium base',
                            'link' => '/',
                        ],
                    ]
                ],
                'SectionCommiment' => [
                    [
                        'image' => '/images/24.webp',
                        'name' => 'Commit 1',
                        'desc' => 'Commit 1',
                    ],

                ],
                'SectionImages' => [
                    [
                        'image' => '/images/24.webp',
                        'link' => '/',
                    ],
                    [
                        'image' => '/images/24.webp',
                        'link' => '/',
                    ],
                    [
                        'image' => '/images/24.webp',
                        'link' => '/',
                    ],
                    [
                        'image' => '/images/24.webp',
                        'link' => '/',
                    ],
                    [
                        'image' => '/images/24.webp',
                        'link' => '/',
                    ],
                ]
            ],
        ]);
        Appearance::create([
            'type' => 'about',
            'value' => [
                'content' => '<h2 style="margin-left:0px;text-align:center;"><strong>SELCO USA, INC., Providing the Offshore Community Technical Solutions</strong></h2><figure class="image" style="height:auto;"><img style="aspect-ratio:396/41;" src="https://selcousa.com/wp-content/uploads/2017/02/SELCO-USA-LOGO-500x51-300x31.png" alt="" srcset="https://selcousa.com/wp-content/uploads/2017/02/SELCO-USA-LOGO-500x51-300x31.png 300w, https://selcousa.com/wp-content/uploads/2017/02/SELCO-USA-LOGO-500x51.png 500w" sizes="100vw" width="396" height="41"></figure><p style="margin-left:0px;">We at Selco USA, Inc. have extensive knowledge of the marine industry. Our product lines, which originated in Europe in 1961, are now shipped to more than 85 nations each year. Whether you need a Ground Fault Monitor and Detector for early detection of immediate and trending ground faults, Arc Protection Relay for unparalleled switchgear protection, or any other protective device, we can help!</p><p style="margin-left:0px;">Backed by more than 60 years of experience, our knowledge and capabilities allow us to provide custom turnkey solutions to all of our clients in a short amount of time. We have long represented the following businesses:</p><p style="margin-left:0px;"><a href="http://hkinstruments.fi/"><span style="color:hsl(0, 75%, 60%);"><u>HK INSTRUMENTS</u></span></a><span style="color:hsl(0, 75%, 60%);"> </span>– USER FRIENDLY MEASURING DEVICES<br><a href="http://www.megacon.com/default.aspx"><span style="color:hsl(0, 75%, 60%);"><u>MEGACON</u></span></a><span style="color:hsl(0, 75%, 60%);">&nbsp;</span>– INNOVATION BEYOND TRADITION<br><a href="http://saci.es/en/"><u>SACI</u></a>&nbsp;– INSTRUMENTATION<br><a href="http://www.selco.com/"><u>SELCO</u></a>&nbsp;– GENERATOR CONTROL AND PROTECTION<br><a href="http://www.ndmeter.co.uk/"><u>NORTHERN DESIGN</u></a>&nbsp;– LEADERS THROUGH INNOVATION<br><a href="http://www.littelfuse.com/"><u>LITTELFUSE</u></a>&nbsp;-EXPERIENCE APPLIED, ANSWERS DELIVERED</p><p style="margin-left:0px;">Our standardized product range is manufactured to the highest standards and designed to work within harsh marine conditions. Through innovation, knowledge, and expertise, we are able to meet the challenges and needs of the Marine Industry. Our Generator Controller Module offers consistent &amp; dependable performance; it’s a multi-featured module used to monitor and control generators and provide system protection.</p><p style="margin-left:0px;">Our commitment to the finest design and unmatched workmanship can be seen in every product we design; be it a flexgen, synchroscope, or alarm panel. We can help you choose the right product for your maritime needs &amp; ensure your operations run smoothly. With a comprehensive product range, including insulation detection devices, motorized potentiometer, and ground fault monitor solutions, together with our team’s extensive knowledge, we are confident to bring a real difference to your business.</p><p style="margin-left:0px;">We provide phone and email support at no charge, but for field service worldwide, we charge a minimal fee.</p><p style="margin-left:0px;">To reach us feel free to use one of these means:<br>e: <a href="mailto:info@selcousa.com"><u>info@selcousa.com</u></a><br>p: <a href="tel:7704559110"><u>+1-770-455-9110</u></a><br>a: 4560 River Bottom Drive, Norcross, GA 30092<br>or any of the social medias shown below</p>',
            ],
        ]);
    }
}
