<?php

return [
    'artist_title_required' => 'Не заполнено название.',
    'artist_title_max' => 'Слишком длинное название (не более :max символов).',
    'artist_country_exists' => 'Выбранная страна не существует.',

    'user_name_required' => 'Не заполнено имя.',
    'user_name_max' => 'Слишком длинный текст в поле имени (не более :max символов).',
    'user_email_required' => 'Не заполнен Email',
    'user_email_email' => 'Введённый Email некорректен.',
    'user_email_max' => 'Слишком длинный текст в поле Email (не более :max символов).',
    'user_email_unique' => 'Такой Email уже существует.',
    'user_password_required' => 'Не заполнен пароль.',
    'user_password_min' => 'Слишком короткий пароль (минимум :min символов).',
    'user_password_confirm_required' => 'Повторите пароль.',
    'user_password_confirm_same' => 'Пароли не совпадают.',

    'artist_has_albums_error' => 'У данного исполнителя есть альбомы. Сначала удалите их.',

    'album_artist_id_required' => 'Не выбран исполнитель.',
    'album_artist_id_exists' => 'Выбранный исполнитель не существует.',
    'album_year_integer' => 'Год должен быть целым числом.',
    'album_year_min' => 'Год не может быть ранее :min.',
    'album_year_max' => 'Год не может быть позднее :max.',
    'album_song_count_integer' => 'Количество песен должно быть целым числом.',
    'album_song_count_min' => 'Количество песен не может быть отрицательным.',

    'country_code_required' => 'Не заполнено код.',
    'country_code_size' => 'Код должен быть двухбуквенным.',
    'country_code_unique' => 'Такой код уже существует.',
];
