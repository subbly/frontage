Subbly's Frontage
===

A simple as ABC Handlebar based template system for Subbly CMS.


## Exemple

    {{#products with {
        "category": "men"
      , "includes": ["images"]
    } }}
      <li>
        <a href="{{route 'product' this}}">{{name}}</a>
        {{created_at}}
      </li>
    {{/products}}
    </ul>

More information soon.
