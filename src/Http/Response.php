<?php

namespace Geekbrains\LevelTwo\Http;

 abstract class Response
{
    // Маркировка успешности ответа
    protected const SUCCESS = true;

     /**
      * @throws \JsonException
      */
     public function send(): void
    {
        // Данные ответа:
        // маркировка успешности и полезные данные
        $data = ['success' => static::SUCCESS] + $this->payload();
        // Отправляем заголовок, говорщий, что в теле ответа будет JSON
        header('Content-Type: application/json');
        // Кодируем данные в JSON и отправляем их в теле ответа
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
        // Декларация абстрактного метода,
        // возвращающего полезные данные ответа
    abstract protected function payload(): array;
}
