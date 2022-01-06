<?php
/* Template Name: Vie Login */
get_header();

the_content();
global $action_message;

if ( isset( $action_message ) ) { ?>
<script>
var wp_message = '<?php echo wp_kses_post( $action_message ); ?>';
</script>
<?php } ?>


<section id="member-login-app" class="container max-width-xs text-component block-content padding-y-xl">

  <transition name="fade" mode="out-in">


    <div v-if="'login' === current_vue" key="1">

      <h3>Connection</h3>

      <form action="#" v-on:submit.prevent="submitLogin" v-if="!hide_form">

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="username">Identifiant&nbsp;<span class="required">*</span></label>
          <input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="username" id="username"  v-model="form_username" :disabled="is_loading" required>			
        </p>

        <p v-if="display_password" class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="username">Mot de passe&nbsp;<span class="required">*</span></label>
          <input type="password" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="password" id="password"  v-model="form_password" :disabled="is_loading" required>			
        </p>

        <p class="form-row">
          <button  role="submit" :disabled="is_loading" class="btn btn--primary width-100% woocommerce-button woocommerce-form-login__submit">Valider</button>
        </p>

      </form>

      <div class="flex justify-between items-center margin-bottom-md margin-top-xs">
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('register')" class="link color-inherit text-sm">Inscription</a>
        </p>
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('forgot_password')" class="link color-inherit text-sm">Mot de passe perdu?</a>
        </p>
      </div>

    </div>

    <div v-else-if="'forgot_password' === current_vue" key="2">

      <h3>Mot de passe oublié</h3>

      <form action="#" v-on:submit.prevent="submitPasswordReset" class="woocommerce-form woocommerce-form-login login" v-if="!hide_form">

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="username">Identifiant&nbsp;<span class="required">*</span></label>
          <input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="username" id="username"  v-model="form_username" :disabled="is_loading" required>			
        </p>

        <p class="form-row">
          <button  role="submit" :disabled="is_loading" class="btn btn--primary width-100% woocommerce-button woocommerce-form-login__submit">Réinitialiser le mot de passe</button>
        </p>

      </form>

      <div class="flex justify-between items-center margin-bottom-md margin-top-xs">
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('register')" class="link color-inherit text-sm">Inscription</a>
        </p>
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('login')" class="link color-inherit text-sm">Retour à la connection</a>
        </p>
      </div>
      
    </div>



    <div v-else-if="'register' === current_vue" key="3">

      <h3>Inscription</h3>
      
      <form action="#" v-on:submit.prevent="submitSubscribe" class="woocommerce-form woocommerce-form-login login" v-if="!hide_form">
        
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="username">Identifiant&nbsp;<span class="required">*</span></label>
          <input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="username" id="username"  v-model="form_username" :disabled="is_loading" required>			
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="username">Courriel&nbsp;<span class="required">*</span></label>
          <input type="email" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="email" id="email"  v-model="form_email" :disabled="is_loading" required>			
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="last_name">Nom&nbsp;<span class="required">*</span></label>
          <input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="last_name" id="last_name"  v-model="form_last_name" :disabled="is_loading" required>			
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="first_name">Prénom&nbsp;<span class="required">*</span></label>
          <input type="text" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="first_name" id="first_name"  v-model="form_first_name" :disabled="is_loading" required>			
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="password_1">Mot de passe&nbsp;<span class="required">*</span></label>
          <input type="password" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="password_1" id="password_1"  v-model="form_password_1" :disabled="is_loading" required>			
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="password_2">Confirmation du mot de passe&nbsp;<span class="required">*</span></label>
          <input type="password" class="form-control woocommerce-Input woocommerce-Input--text input-text width-100%" name="password_2" id="password_2"  v-model="form_password_2" :disabled="is_loading" required>			
        </p>
              
        <p class="form-row">
          <button  role="submit" :disabled="is_loading" class="btn btn--primary width-100% woocommerce-button woocommerce-form-login__submit">Inscription</button>
        </p>

      </form>

      <div class="flex justify-between items-center margin-bottom-md margin-top-xs">
        <p class="woocommerce-LostPassword lost_password">
        </p>
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('login')" class="link color-inherit text-sm">Retour à la connection</a>
        </p>
      </div>
      
    </div>



    <div v-else-if="'complete' === current_vue" key="4">

      <div class="flex justify-between items-center margin-bottom-md margin-top-xs">
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('register')" class="link color-inherit text-sm">Inscription</a>
        </p>
        <p class="woocommerce-LostPassword lost_password">
          <a href="#" v-on:click.prevent="displayVue('forgot_password')" class="link color-inherit text-sm">Mot de passe perdu?</a>
        </p>
      </div>

    </div>

  </transition>

  
  <div v-if="is_loading" class="login_loader circle-loader circle-loader--v1" role="alert">
    <div aria-hidden="true">
      <div class="circle-loader__circle"></div>
    </div>
  </div>


  <div v-if="message.length > 0" role="alert" class="login_msg fail bg-error bg-opacity-20% padding-xs radius-md text-sm color-contrast-higher margin-top-xxs" v-html="message"></div>



</section>






<?php get_footer();
