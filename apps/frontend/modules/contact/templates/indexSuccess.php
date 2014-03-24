<h1>Contacts List</h1>

<table cellspacing="15">
  <thead>
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Phone</th>
      <th>Twitter handle</th>
      <th>Twitter follower count</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($contacts as $contact): ?>
    <tr>
      <td><a href="<?php echo url_for('contact/show?id='.$contact->getId()) ?>"><?php echo $contact->getId() ?></a></td>
      <td><?php echo $contact->getName() ?></td>
      <td><?php echo $contact->getPhone() ?></td>
      <td><?php echo $contact->getTwitterHandle() ?></td>
      <td><?php include_component('contact', 'followerCount', array('twitterHandle' => $contact->getTwitterHandle()));?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('contact/new') ?>">New</a>
