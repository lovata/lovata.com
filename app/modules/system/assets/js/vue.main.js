// Configure bluebird
(function() {
    Promise.config({
        cancellation: true
    });

    Queue.configure(Promise);
})();
