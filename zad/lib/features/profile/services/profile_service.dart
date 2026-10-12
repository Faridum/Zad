import '../../../shared/services/api_service.dart';


class ProfileService {
  static Future<void> updateProfile({
    required String name,
    required String email,
    required String phone,
  }) async {
    final response = await ApiService.post(
      'profile/update.php', // عدّل المسار حسب الباك اند لديك
      body: {
        'name': name,
        'email': email,
        'phone': phone,
      },
    );


    if (!response.success) {
      throw Exception(response.message ?? 'Failed to update profile');
    }
  }
}

