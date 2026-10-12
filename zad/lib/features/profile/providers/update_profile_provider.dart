import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../shared/services/api_service.dart';
import '../../../core/storage/local_storage.dart';

final updateProfileProvider =
FutureProvider.family<void, Map<String, String>>((ref, data) async {
  final token = await LocalStorage.getToken();

  if (token == null || token.isEmpty) {
    throw Exception("No token found");
  }

  final response = await ApiService.post(
    'auth/update_profile.php',

    body: data,
  );

  if (!response.success) {
    throw Exception(response.message);
  }
});

