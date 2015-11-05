##Тестовое задание для веб-разработчика

Необходимо написать скрипт, собирающий данные с рейтинга Кинопоиска:
[http://www.kinopoisk.ru/level/20/](http://www.kinopoisk.ru/level/20/)
, и сохраняющего позицию, рейтинг, оригинальное название, год и кол-во проголосовавших людей в БД (любой на выбор). 
Также необходимо добавить соответствующие поля в БД для выборки рейтинга на определенную дату. 
Скрипт должен быть написан с учетом возможности постановки в cron.
Также необходимо создать базовую веб-страницу, выводящую топ-10 фильмов на указанную дату. 
На ней должно присутствовать поле, где пользователь может указать дату выборки. 
При выгрузке данных из СУБД должен быть использован кэширующий слой, 
что бы избежать запросов к базе, каждый раз, когда рейтинг должен быть показан.

__Критерии оценки:__
 * чистый, читаемый, структурируемый php код, объектное ориентированный дизайн
 * схема базы данных
 * чистота верстки