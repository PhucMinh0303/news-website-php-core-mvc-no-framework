<?php
/**
 * Recruitment Job Detail Page - Job Application
 */
?>
<main class="section10">
  <section class="f_td_D">
    <div class="min_wrap2">
      <article class="ct_page">
        <div class="ct_td_D">
          <div class="til_td_D">
            <h1 class="til_news_D">Trưởng phòng nguồn vốn</h1>

            <div class="share_D">
              <span>Share</span>

              <ul class="list_share_D">
                <li>
                  <a
                    class="copy_links"
                    href="javascript:void(0)"
                    title="Copy link"
                    val="<?php echo View::url('recruitment/truong-phong-nguon-von'); ?>"
                  >
                    <i class="fa-solid fa-link"></i>
                  </a>
                </li>

                <li>
                  <a
                    href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(BASE_URL . 'recruitment/truong-phong-nguon-von'); ?>"
                    target="_blank"
                    title="Chia sẻ bài viết lên Facebook"
                  >
                    <i class="fa-brands fa-facebook-f"></i>
                  </a>
                </li>

                <li>
                  <a
                    href="http://www.twitter.com/share?url=<?php echo urlencode(BASE_URL . 'recruitment/truong-phong-nguon-von'); ?>"
                    target="_blank"
                    title="Chia sẻ bài viết lên Twitter"
                  >
                    <i class="fa-brands fa-twitter"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <!--end til_td_D-->

          <div class="f-detail clearfix">
            <strong>Mô tả công việc</strong>
            <ul>
              <li>
                Quản lý, theo dõi nhu cầu và cấp phát văn phòng phẩm, mực in,
                nước uống.
              </li>
              <li>
                Thực hiện các thủ tục thanh toán chi phí hành chính văn phòng.
              </li>
              <li>Soạn thảo, lưu trữ hồ sơ, giấy tờ liên quan.</li>
              <li>Tổ chức sự kiện và truyền thông nội bộ.</li>
              <li>Quản lý website, fanpage của công ty.</li>
              <li>Thực hiện các công việc khác theo chỉ đạo của cấp trên.</li>
            </ul>
            <strong>Yêu cầu công việc</strong>

            <ul>
              <li>
                Tốt nghiệp đại học chuyên ngành Kinh tế, Tài chính, Ngân hàng
                hoặc các ngành liên quan.
              </li>
              <li>
                Ưu tiên ứng viên có kinh nghiệm trong lĩnh vực tài chính, ngân
                hàng.
              </li>
              <li>Có khả năng giao tiếp và thuyết trình tốt.</li>
              <li>Tinh thần trách nhiệm cao, làm việc cẩn thận, tỉ mỉ.</li>
              <li>Có khả năng làm việc độc lập và theo nhóm.</li>
            </ul>
            <strong>Quyền lợi</strong>

            <ul>
              <li>Lương thỏa thuận: 7 - 10 triệu đồng/tháng.</li>
              <li>
                Lương tháng 13 và thưởng các ngày Lễ, Tết theo quy định của công
                ty.
              </li>
              <li>Tham gia BHYT, BHXH theo quy định của Nhà nước.</li>
              <li>Du lịch, nghỉ mát hằng năm.</li>
              <li>
                Được đào tạo kỹ năng chuyên môn, làm việc trong môi trường thân
                thiện, khuyến khích sáng tạo.
              </li>
            </ul>
            <br />
            <div>
              Liên hệ trực tiếp với phòng HCNS:<br />
              Điện thoại/Zalo: Chị Phương – PHCNS: 0999 678 6789<br />
              Email: tuyendung@capitalam.vn<br />
              Bộ phận:&nbsp;Hành chính/Nhân sự
            </div>
          </div>
          <!--end f-detail-->
        </div>
        <!--end ct_td_D-->
      </article>
      <!--end ct_page-->

      <aside class="sb_page">
        <div class="sb_td_D sty_sticky">
          <div class="if_td_D">
            <h3 class="til_sb_td_D">Thông tin việc làm</h3>

            <ul class="list_if_td_D">
              <li>
                <strong>Nơi làm việc:</strong>

                <p>
                  Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ
                  Chí Minh
                </p>
              </li>

              <li>
                <strong>Bằng cấp:</strong>

                <p>Cao Đẳng - Đại Học</p>
              </li>

              <li>
                <strong>Số lượng tuyển:</strong>

                <p>01</p>
              </li>

              <li>
                <strong>Hạn chốt nhận hồ sơ:</strong>

                <p>22-11-2025</p>
              </li>
            </ul>
          </div>
          <!--end if_td_D-->

          <div class="but_fcb_td" data-src="#fcb_td" data-fancybox="">
            <i class="fa-regular fa-user"></i>

            Ứng tuyển vị trí này
          </div>
          <!-- Overlay -->
          <div class="popup-overlay" id="popupOverlay"></div>

          <form
            id="fcb_td"
            class="fcb_td"
            method="post"
            enctype="multipart/form-data"
            action="<?php echo View::url('recruitment/apply'); ?>"
          >
            <input
              style="display: none"
              type="text"
              name="key_check"
              value="170252dbe3ad8cb7dac13d255a77c5de"
            />
            <button type="button" class="close-popup">&times;</button>
            <!-- Nút đóng mới -->

            <h3 class="t_fcb_td">Nộp đơn ứng tuyển</h3>

            <ul class="ul_r_f_contact">
              <li>
                <input
                  type="text"
                  placeholder="Họ tên *"
                  name="ten"
                  value=""
                  class="ipt_f_contact box-sizing-fix"
                />

                <span class="icon_r_f_contact">
                  <i class="fa-regular fa-user"></i>
                </span>
              </li>

              <li>
                <input
                  type="text"
                  placeholder="Điện thoại *"
                  name="dt"
                  value=""
                  class="ipt_f_contact box-sizing-fix"
                />

                <span class="icon_r_f_contact">
                  <i class="fas fa-phone-alt"></i>
                </span>
              </li>

              <li>
                <input
                  type="text"
                  placeholder="Email *"
                  name="email"
                  value=""
                  class="ipt_f_contact box-sizing-fix"
                />

                <span class="icon_r_f_contact">
                  <i class="fa-regular fa-envelope"></i>
                </span>
              </li>

              <li>
                <textarea
                  placeholder="Nội dung"
                  name="noidung"
                  class="txt_f_contact box-sizing-fix"
                ></textarea>
              </li>

              <li>
                <strong> Hồ sơ của bạn: (Cho nộp file CV) </strong>
                <br />

                <input type="file" name="filechon" />
              </li>
            </ul>
            <!-- End .ul_r_f_contact -->

            <button type="submit" name="guituyendung" class="but_contact">
              Ứng tuyển
            </button>
          </form>

          <!--end .fcb_td-->
        </div>
        <!--end sb_td_D-->
      </aside>
      <!--end sb_page-->
    </div>
    <!--end min_wrap-->
  </section>
  <section class="f_td r_p36">
    <div class="min_wrap_recruitment">
      <div class="tit_cont_1">
        <h2 class="na_til_cont">Tuyển dụng khác</h2>
      </div>

      <ul class="list_td">
        <li>
          <div class="c1_list_td">
            <a href="<?php echo View::url('recruitment/truong-phong-nguon-von'); ?>" title="Trưởng phòng nguồn vốn">
              <figure class="img_list_td">
                <img
                  src="<?php echo View::asset('img/recruitment/truong-phong-nguon-von-1763953822-egprx.webp'); ?>"
                  alt="Trưởng phòng nguồn vốn"
                />
              </figure>
            </a>

            <div class="if_list_td">
              <h3 class="na_list_td link_hv">
                <a
                  href="<?php echo View::url('recruitment/truong-phong-nguon-von'); ?>"
                  class="link_hv"
                  title="Trưởng phòng nguồn vốn"
                  >Trưởng phòng nguồn vốn</a
                >
              </h3>

              <p>
                Nơi làm việc: Hội sở: Hasco Building, 98 Xuân Thủy, Phường An
                Khánh, Tp. Hồ Chí Minh
              </p>

              <ol>
                <li>
                  Bằng cấp:

                  <strong>Cao Đẳng - Đại Học</strong>
                </li>

                <li>
                  Số lượng tuyển:

                  <strong>01</strong>
                </li>
              </ol>
            </div>
          </div>

          <div class="c2_list_td">
            <div class="date_list_td">
              <span>Hạn nộp hồ sơ</span>

              <strong>22-11-2025</strong>
            </div>

            <div class="but_list_td">
              <a
                href="<?php echo View::url('recruitment/truong-phong-nguon-von'); ?>"
                class="but_03"
                title="Trưởng phòng nguồn vốn"
              >
                Xem chi tiết
              </a>
            </div>
          </div>
        </li>

        <li>
          <div class="c1_list_td">
            <a href="<?php echo View::url('recruitment/chuyen-vien-dau-tu'); ?>" title="Chuyên viên đầu tư">
              <figure class="img_list_td">
                <img
                  src="<?php echo View::asset('img/recruitment/truong-phong-nguon-von-1763953822-egprx.webp'); ?>"
                  alt="Chuyên viên đầu tư"
                />
              </figure>
            </a>

            <div class="if_list_td">
              <h3 class="na_list_td link_hv">
                <a href="<?php echo View::url('recruitment/chuyen-vien-dau-tu'); ?>" class="link_hv" title="Chuyên viên đầu tư"
                  >Chuyên viên đầu tư</a
                >
              </h3>

              <p>
                Nơi làm việc: Hội sở: Hasco Building, 98 Xuân Thủy, Phường An
                Khánh, Tp. Hồ Chí Minh
              </p>

              <ol>
                <li>
                  Bằng cấp:

                  <strong>Cao Đẳng - Đại Học</strong>
                </li>

                <li>
                  Số lượng tuyển:

                  <strong>01</strong>
                </li>
              </ol>
            </div>
          </div>

          <div class="c2_list_td">
            <div class="date_list_td">
              <span>Hạn nộp hồ sơ</span>

              <strong>22-11-2025</strong>
            </div>

            <div class="but_list_td">
              <a href="<?php echo View::url('recruitment/chuyen-vien-dau-tu'); ?>" class="but_03" title="Chuyên viên đầu tư">
                Xem chi tiết
              </a>
            </div>
          </div>
        </li>

        <li>
          <div class="c1_list_td">
            <a
              href="<?php echo View::url('recruitment/chuyen-vien-hanh-chinh'); ?>"
              title="Chuyên viên hành chính"
            >
              <figure class="img_list_td">
                <img
                  src="<?php echo View::asset('img/recruitment/truong-phong-nguon-von-1763953822-egprx.webp'); ?>"
                  alt="Chuyên viên hành chính"
                />
              </figure>
            </a>

            <div class="if_list_td">
              <h3 class="na_list_td link_hv">
                <a href="<?php echo View::url('recruitment/chuyen-vien-hanh-chinh'); ?>" class="link_hv" title="Chuyên viên hành chính"
                  >Chuyên viên hành chính</a
                >
              </h3>

              <p>
                Nơi làm việc: Hội sở: Hasco Building, 98 Xuân Thủy, Phường An
                Khánh, Tp. Hồ Chí Minh
              </p>

              <ol>
                <li>
                  Bằng cấp:

                  <strong>Cao Đẳng - Đại Học</strong>
                </li>

                <li>
                  Số lượng tuyển:

                  <strong>01</strong>
                </li>
              </ol>
            </div>
          </div>

          <div class="c2_list_td">
            <div class="date_list_td">
              <span>Hạn nộp hồ sơ</span>

              <strong>22-11-2025</strong>
            </div>

            <div class="but_list_td">
              <a href="<?php echo View::url('recruitment/chuyen-vien-hanh-chinh'); ?>" class="but_03" title="Chuyên viên hành chính">
                Xem chi tiết
              </a>
            </div>
          </div>
        </li>

        <li>
          <div class="c1_list_td">
            <a href="<?php echo View::url('recruitment/chuyen-vien-nhan-su'); ?>" title="Chuyên viên nhân sự">
              <figure class="img_list_td">
                <img
                  src="<?php echo View::asset('img/recruitment/truong-phong-nguon-von-1763953822-egprx.webp'); ?>"
                  alt="Chuyên viên nhân sự"
                />
              </figure>
            </a>

            <div class="if_list_td">
              <h3 class="na_list_td link_hv">
                <a href="<?php echo View::url('recruitment/chuyen-vien-nhan-su'); ?>" class="link_hv" title="Chuyên viên nhân sự"
                  >Chuyên viên nhân sự</a
                >
              </h3>

              <p>
                Nơi làm việc: Hội sở: Hasco Building, 98 Xuân Thủy, Phường An
                Khánh, Tp. Hồ Chí Minh
              </p>

              <ol>
                <li>
                  Bằng cấp:

                  <strong>Cao Đẳng - Đại Học</strong>
                </li>

                <li>
                  Số lượng tuyển:

                  <strong>01</strong>
                </li>
              </ol>
            </div>
          </div>

          <div class="c2_list_td">
            <div class="date_list_td">
              <span>Hạn nộp hồ sơ</span>

              <strong>21-11-2025</strong>
            </div>

            <div class="but_list_td">
              <a href="<?php echo View::url('recruitment/chuyen-vien-nhan-su'); ?>" class="but_03" title="Chuyên viên nhân sự">
                Xem chi tiết
              </a>
            </div>
          </div>
        </li>
      </ul>
      <!--end list_td-->
    </div>
    <!--end min_wrap-->
  </section>
</main>
