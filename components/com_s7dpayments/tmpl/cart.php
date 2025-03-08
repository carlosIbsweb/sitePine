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
<a class="nav-link menu-user" href="#">Olá! <?= $userid ? explode(" ",$user->name)[0] : 'Faça login'; ?></a>
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

	<a class="pine-btn-logout"><?= $userid ? '<i class="fa fa-sign-out"></i>' : '<i class="fa fa-user"></i>';?> <?php require_once('default_logout.php'); ?></a>
</nav>

<script>
        // Função para abrir/fechar o menu lateral
        function toggleMenu() {
			jQuery(function($){
				$('#sidebar').toggleClass('active')
				$('#overlay').toggleClass('active')
			})
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