# Данные и модели

Все логические модели данных хранятся в JSON-файлах в директории `/data`. Каждая запись в файле имеет уникальное поле `id`, которое генерируется и используется только сервером.

## Пример: `sites.json`

Эта модель описывает сложный ресурс "сайт", который включает в себя вложенные страницы и ссылки на референсы.

```json
{
  "id": "site-1",
  "name": "Landing Project",
  "slug": "landing-project",
  "repositoryUrl": "https://github.com/example/repo",
  "figmaUrl": "https://www.figma.com/file/xxx",
  "referenceColumns": ["main", "mobile"],
  "pages": [
    {
      "id": "site-page-1",
      "name": "Главная",
      "url": "landing-project/index.html",
      "references": [
        "references/sites/landing-project/header.png",
        "references/sites/landing-project/footer.png"
      ]
    }
  ]
}
```

## Пример: `labs.json`

Эта модель описывает простой ресурс "лабораторная работа", который ссылается на один PDF-файл.

```json
{
  "id": "lab-1",
  "number": 1,
  "fileName": "ЛР1_Иванов_И.И.pdf",
  "url": "labs/ЛР1_Иванов_И.И.pdf"
}
```
