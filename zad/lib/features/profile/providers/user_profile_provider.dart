import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../shared/services/api_service.dart';


final userProfileProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final response = await ApiService.get('auth/profile.php');


  if (!response.success) {
    throw Exception(response.message);
  }


  return response.data as Map<String, dynamic>;
});

