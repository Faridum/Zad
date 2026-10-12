import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../shared/services/api_service.dart';
import '../../../core/storage/local_storage.dart';

final changePasswordProvider = FutureProvider.family<void, Map<String, String>>(
      (ref, data) async {
    final token = await LocalStorage.getToken();

    if (token == null || token.isEmpty) {
      throw Exception("Unauthorized");
    }

    final response = await ApiService.post(
      "auth/change_password.php",

      body: {
        "current_password": data["current_password"]!,
        "new_password": data["new_password"]!,
        "confirm_password": data["confirm_password"]!,
      },
    );

    if (!response.success) {
      throw Exception(response.message);
    }
  },
);

