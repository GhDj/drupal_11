# Cyber Security Network

## Features

### Feedback pages

To direct the user to a feedback page, redirect to `csn_custom.feedback` and pass
the data to be displayed.

Example:
```php
/** @var \Drupal\csn_custom\FeedbackServiceInterface $service */
$service = \Drupal::service('csn_custom.feedback');
$form_state->setRedirectUrl($service->getFeedbackUrl($this->t('Thank you for your help!'));
```

```php
/** @var \Drupal\csn_custom\FeedbackServiceInterface $service */
$service = \Drupal::service('csn_custom.feedback');
$token = $service->setFeedback([
  'title' => 'Testing',
  'message' => $this->t('Hello world'),
  'content' => [
    '#type' => 'markup',
    '#markup' => '<p>Testing</p>',
  ],
]);

$form_state->setRedirect('csn_custom.feedback', ['token' => $token]);
```
