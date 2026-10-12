class AppConfig {
  static const String _apiBaseUrl = String.fromEnvironment('API_BASE_URL');

  static Uri get apiBaseUri {
    if (_apiBaseUrl.isEmpty) {
      throw StateError(
        'API_BASE_URL is not configured. Pass it with --dart-define.',
      );
    }

    final uri = Uri.parse(_apiBaseUrl);
    if (!uri.hasScheme || !uri.hasAuthority) {
      throw StateError('API_BASE_URL must be an absolute URL.');
    }

    return uri.replace(
      path: uri.path.endsWith('/') ? uri.path : '${uri.path}/',
    );
  }

  static Uri get uploadsBaseUri => apiBaseUri.resolve('../../uploads/');

  static Uri apiUri(String path) =>
      apiBaseUri.resolve(path.replaceFirst(RegExp(r'^/'), ''));
}
