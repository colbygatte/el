# El
Quickly format HTML elements.

`composer require colbygatte/el`

## Example
```php
$data = [
    ['h1', 'h2', 'h3'],
    ['a1', 'a2', 'a3'],
    ['b1', 'b2', 'b3'],
    ['c1', 'c2', 'c3'],
];

$table = el($data)
    ->at(0)->each('th')->top()
    ->slice(1)->depth(1)->each('td')->top()
    ->each('tr')
    ->tag('table')
    ->str();
    
echo $table;
```

Will output (as a single line):
```html
<table>
    <tr>
        <th>h1</th>
        <th>h2</th>
        <th>h3</th>
    </tr>
    <tr>
        <td>a1</td>
        <td>a2</td>
        <td>a3</td>
    </tr>
    <tr>
        <td>b1</td>
        <td>b2</td>
        <td>b3</td>
    </tr>
    <tr>
        <td>c1</td>
        <td>c2</td>
        <td>c3</td>
    </tr>
</table>
```

Breakdown
```php
// Create a new Element.
el($data)
    // At index 0, iterate over it's child elements and tag them with <th>,
    // then move from the index 0 back to the top level.
    ->at(0)->each('th')->top()
    
    // Grab a slice from index 1 to the end of the array,
    // then iterate one level deep, tagging each found element with <td>
    // then move back to the top level.
    ->slice(1)->depth(1)->each('td')->top()
    
    // Now back at the top level, tag each element with tr.
    ->each('tr')
    
    // Tag the entire top level with table.
    ->tag('table')
    
    // Convert to a string.
    // Implements __toString(), so this can be type casted to string or
    // echoed without calling str().
    ->str();
```