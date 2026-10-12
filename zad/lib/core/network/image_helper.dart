import 'package:grad_project/core/constants/app_config.dart';

class ImageHelper {
  static String? resolve(String? path) {
    if (path == null || path.isEmpty) return null;
    if (path.startsWith('http')) return path;
    return AppConfig.uploadsBaseUri.resolve(path).toString();
  }
}
