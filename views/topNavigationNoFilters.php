<div class="top_nav col-md-12" style="min-height: 44px; background-color: #f7f7f7; border-bottom: 1px solid #e6e9ed;">
      <div class="nav_menu" style="min-height: 44px; padding: 0 15px; background-color: #f7f7f7;">
          <nav style="display: flex; align-items: center; justify-content: space-between; min-height: 44px;">
              <div style="display: flex; align-items: center; margin-left: 0;">
                  <a href="index.php" class="btn btn-default btn-sm" title="Voltar para o índice" style="padding: 6px 10px;">
                      <i class="fa fa-arrow-left"></i>
                  </a>
              </div>

              <ul class="nav navbar-nav navbar-right" style="margin-bottom: 0;">
                  <li class="">
                      <a href="javascript:;" class="user-profile dropdown-toggle"
                          data-toggle="dropdown"
                          aria-expanded="false" style="padding: 0 10px; line-height: 44px; color: #2a3f54;">
                          <?php
                            echo $_SESSION['user_name'];
                            ?>
                      </a>
                      <ul class="dropdown-menu dropdown-usermenu pull-right">
                          <li><a href="scripts/logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                      </ul>
                  </li>
              </ul>
          </nav>
      </div>
  </div>