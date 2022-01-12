<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
  window.OneSignal = window.OneSignal || [];
  OneSignal.push(function() {

    // set default title
    // OneSignal.setDefaultTitle("B2B OMS");
OneSignal.push(function() {
    // initialize
    OneSignal.init({
        appId: "e6878a06-25dc-4014-bfad-e9cce643c62a",
        safari_web_id: "web.onesignal.auto.20000090-ae08-4d2f-988d-291912c8a9bf",
        notifyButton: {
            enable: false,
        },
        subdomainName: "b2b-nt-crm",
        allowLocalhostAsSecureOrigin: true, // enable localhost for testing
        autoRegister: false,    // auto register false
        promptOptions: {
            actionMessage: "We'd like to show you important notifications !",
            customlink: {
            enabled: false, /* Required to use the Custom Link */
            style: "button", /* Has value of 'button' or 'link' */
            size: "medium", /* One of 'small', 'medium', or 'large' */
            color: {
                button: '#E12D30', /* Color of the button background if style = "button" */
                text: '#FFFFFF', /* Color of the prompt's text */
            },
            text: {
                subscribe: "Subscribe to push notifications", /* Prompt's text when not subscribed */
                unsubscribe: "Unsubscribe from push notifications", /* Prompt's text when subscribed */
                // explanation: "Get updates from all sorts of things that matter to you", /* Optional text appearing before the prompt button */
            },
            unsubscribeEnabled: true, /* Controls whether the prompt is visible after subscription */
            }
        },
        welcomeNotification:{
            disable: false,
            title: "Welcome to OMS",
            message: "Thank You for subscribing to the Notifications!"
        },
    });
});

    // show prompt if notifications are not enabled
    OneSignal.isPushNotificationsEnabled(function(isEnabled) {
        if (isEnabled){
            console.log("Push notifications are enabled!");
        }else{
            console.log("Push notifications are not enabled yet.");
        }
    });

    // if user clicks on Allow from slidedown prompt then subscribe that user manually
    OneSignal.on('popoverAllowClick', function() {
      // subscribe the tags when user clicks on allow notifications first time
      $("#login").trigger("click");
    });

  });

  $("#logout").on("click",function(){
    OneSignal.push(["setSubscription", false]);

    // get tags
    OneSignal.push(function() {
      OneSignal.getTags().then(function(tags) {
        $.each( tags, function( key, value ) {
          OneSignal.deleteTag(key);
        });
        location.reload();
      });
    });


  });
  $("#login").on("click",function(){
    OneSignal.push(["setSubscription", true]);

    OneSignal.push(function() {

      // first remove any tags if there are any
      OneSignal.getTags().then(function(tags) {
        $.each( tags, function( key, value ) {
          OneSignal.deleteTag(key);
        });
      });

      var tags = {
          "user": "user_"+"{{ @Auth::user()->id }}",
          "role": "role_"+"{{ @Auth::user()->role_id }}",
          @if(@Auth::user()->role_id == 4)
          "customer": "customer_"+"{{ @Auth::user()->customer_id }}",
          "class": "class_"+"{{ @Auth::user()->customer->u_class }}",
          "territory": "territory_"+"{{ @Auth::user()->customer->territory }}",
          @endif
          @if(@Auth::user()->role_id == 2)
          "sales_specialist": "ss_"+"{{ @Auth::user()->id }}",
          @endif
      };

    //   OneSignal.sendTags(tags, function(tagsSent) {
    //     location.reload();
    //   });
    OneSignal.sendTags(tags);
    });
  });
</script>
