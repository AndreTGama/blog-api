<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Conteúdo de posts com blocos de código

O campo `content` aceita tanto o texto simples legado quanto uma lista ordenada de blocos. Use blocos `paragraph` para o texto e `code_tabs` para exibir exemplos selecionáveis no meio do artigo. O mesmo formato pode ser enviado ao criar ou atualizar um post:

```json
{
  "content": [
    {
      "type": "paragraph",
      "text": "O padrão Abstract Factory cria famílias de objetos relacionados."
    },
    {
      "type": "code_tabs",
      "tabs": [
        {
          "label": "PHP",
          "language": "php",
          "code": "interface UIFactory { public function createButton(): Button; }"
        },
        {
          "label": "TypeScript",
          "language": "typescript",
          "code": "interface UIFactory { createButton(): Button; }"
        }
      ]
    },
    {
      "type": "paragraph",
      "text": "O artigo continua normalmente depois do exemplo."
    }
  ]
}
```

A ordem dos itens em `content` é a ordem em que o artigo deve ser exibido. Cada aba precisa de `label`, `language` e `code`. A API salva esses blocos no campo existente e os devolve como uma lista; posts antigos com `content` em texto continuam sendo retornados como texto. A interface do blog é responsável por renderizar a lista e implementar a seleção visual das abas.

## Imagem de capa do post

O campo opcional `cover_image` aceita uma imagem JPEG, PNG ou WebP de até 2 MB no envio multipart/form-data. A imagem é salva no disco público em `posts/thumbnails`, e a API devolve sua URL no campo `cover_image`. Para disponibilizar arquivos locais, execute `php artisan storage:link`. URLs de imagem já existentes continuam aceitas para manter compatibilidade.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
