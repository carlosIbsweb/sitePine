<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/

// No direct access.
defined('_JEXEC') or die;

$user = JFactory::getUser();
$userid = $user->id;

$count = count(json_decode(paymentsCart::getCart($cartid,'cartid','products'),true));
?>


 <!-- Botão do menu hambúrguer -->
 <div class="menu-btn" onclick="toggleMenu()">
        <i class="fa fa-bars"></i>
    </div>

<!-- Overlay -->
<div class="overlay menu-pine-overlay" id="overlay" onclick="toggleMenu()"></div>

<!-- Sidebar -->
<nav class="sidebar sidebar-pine" id="sidebar">
<a class="nav-link menu-user" href="#">Olá, <?= explode(" ",$user->name)[0]; ?></a>
	<a href="javascript:void(0)" class="close-btn close-link" onclick="toggleMenu()">
		<i class="fa fa-times"></i>
	</a>
	
	<!-- Dropdown de Minhas Inscrições -->
	<div class="submenu">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<i class="fa fa-shopping-cart"></i> Meu Carrinho
		</a>
		<div class="dropdown-menu dropdown-menu-pine">
		<p class="dropdown-item-text"><?= $count == 0 ? ' Vazio' : ($count >= 2 ? $count.' Items' : $count.' Item'); ?></p>
  	<a class="dropdown-item" href="<?= $url;?>?cart">Ver carrinho</a>
		</div>
	</div>
	<a href="<?= $url;?>?minhas-inscricoes"> <i class="fa fa-list-alt"></i> Minhas Inscrições</a>

	<a class="pine-btn-logout"><i class="fa fa-sign-out"></i> <?php !empty($userid) ? require_once('default_logout.php') : null; ?></a>
</nav>

<script>
        // Função para abrir/fechar o menu lateral
        function toggleMenu() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("overlay").classList.toggle("active");
        }

        // Habilitar dropdown manualmente no Bootstrap 4.1
        $(document).ready(function() {
            $('.dropdown-toggle').click(function(event) {
                event.preventDefault();
                $(this).next('.dropdown-menu').slideToggle(200,'slow');
            });

			$('.menu-pine-overlay').appendTo('body')
			$('.sidebar-pine').appendTo('body')
        });
    </script>