import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../shared/services/api_service.dart';
import '../../../core/storage/local_storage.dart';

// final userProfileProvider = FutureProvider<Map<String, dynamic>>((ref) async {
//   final token = await LocalStorage.getToken();
//
//   if (token == null) {
//     throw Exception("No token found");
//   }
//
//   final response = await ApiService.get(
//     "auth/profile.php",
//     token: token,
//   );
//
//   if (!response.success) {
//     throw Exception(response.message);
//   }
//
//   return response.data as Map<String, dynamic>;
// });
final userProfileProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final response = await ApiService.get("auth/profile.php");

  if (!response.success) {
    throw Exception(response.message);
  }

  return response.data;
});


