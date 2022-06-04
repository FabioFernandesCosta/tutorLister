<html>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript">
    typeaheadInit()
    function typeaheadInit() {

        console.log("test");
        var route = "{{ url("consultar") }}";
        $("{{ $campo }}").typeahead({
            source: function(query, process) {
                return $.get(route, {
                    term: "{{ $campo }}_" + query
                }, function(data) {
                    return process(data);
                });
            }
        });
    }
</script>

</html>
