<html>
    <div class="form-field" id="involv">
        <input type="text" name="InvolvedUsers[]" id="usuario" list="users">
        @include('autocomplete', ['campo' => 'usuario'])
    </div>
</html>