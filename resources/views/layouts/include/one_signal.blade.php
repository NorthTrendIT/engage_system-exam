<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
window.OneSignal = window.OneSignal || [];
OneSignal.push(function() {
    OneSignal.init({
        appId: "e6878a06-25dc-4014-bfad-e9cce643c62a",
        safari_web_id: "web.onesignal.auto.20000090-ae08-4d2f-988d-291912c8a9bf",
        notifyButton: {
            enable: true,
        },
        subdomainName: "b2b-nt-crm",
        autoRegister: false,    // auto register false
        promptOptions: {
            slidedown: {
                prompts: [{
                    type: "push", // current types are "push" & "category"
                    autoPrompt: true,
                    text: {
                        /* limited to 90 characters */
                        actionMessage: "We'd like to show you notifications for the latest news and updates.",
                        /* acceptButton limited to 15 characters */
                        acceptButton: "Allow",
                        /* cancelButton limited to 15 characters */
                        cancelButton: "Cancel"
                    },
                    delay: {
                    pageViews: 1,
                    timeDelay: 20
                    }
                }]
            }
        },
        welcomeNotification:{
            disable: false,
            title: "Welcome to OMS",
            message: "Thank You for subscribing to the Notifications!",
        },
    });

    OneSignal.push(function() {
        OneSignal.showSlidedownPrompt();
    });

    // show prompt if notifications are not enabled
    OneSignal.isPushNotificationsEnabled(function(isEnabled) {
        if (isEnabled){
            console.log("Push notifications are enabled!");
        }else{
            console.log("Push notifications are not enabled yet.");
        }
    });

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

        var tagsCounts = 3; // random number between 1 and 10
        var tags = {};

        for(i=1;i<=tagsCounts;i++){
            var randnumber = Math.ceil((Math.random() * 50) + 1);
            var keyvalue = "class_"+randnumber;

            tags[keyvalue] = keyvalue;
        }


        OneSignal.sendTags(tags, function(tagsSent) {
            location.reload();
        });
    });
});
</script>
