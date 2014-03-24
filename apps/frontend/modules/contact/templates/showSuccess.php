<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $contact->getId() ?></td>
    </tr>
    <tr>
      <th>Name:</th>
      <td><?php echo $contact->getName() ?></td>
    </tr>
    <tr>
      <th>Phone:</th>
      <td><?php echo $contact->getPhone() ?></td>
    </tr>
    <tr>
      <th>Twitter handle:</th>
      <td><?php echo $contact->getTwitterHandle() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('contact/edit?id='.$contact->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('contact/index') ?>">List</a>
